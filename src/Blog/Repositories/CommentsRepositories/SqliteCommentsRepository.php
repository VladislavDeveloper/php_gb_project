<?php

namespace Blog\Repositories\CommentsRepositories;
use Blog\Comment\Comment;
use Blog\Exceptions\CommentNotFoundException;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\UUID\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $commentUuid = $comment->getUuid();

        $statement->execute([
            ':uuid' => (string)  $commentUuid,
            ':post_uuid' => (string) $comment->getPost()->getUuid(),
            ':author_uuid' => (string) $comment->getAuthor()->uuid(),
            ':text' => $comment->getText(),
        ]);

        //Логируем сообщение о том, что комментарий сохранен
        $this->logger->info("Comment saved:  $commentUuid");
    }
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result == false){

            //Логируем сообщение о том, что комментарий не найден
            $this->logger->warning("Comment not found: $uuid");

            throw new CommentNotFoundException(
                'Comment not found'
            );
        }

        $postsRepository = new SqlitePostsRepository($this->connection, $this->logger);

        $post = $postsRepository->get(new UUID($result['post_uuid']));

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);

        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
        
    }

    //Метод для получения массива комментариев к посту по post_uuid
    public function getCommentsByPost(UUID $post_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => $post_uuid
        ]);

        $postsRepository = new SqlitePostsRepository($this->connection, $this->logger);

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);

        $commentsList = $statement->fetchAll();

        if(!$commentsList){
            $this->logger->warning("No comments to post: $post_uuid");
            
            throw new CommentNotFoundException(
                'Comment not found'
            );
        }

        $comments = [];

        foreach($commentsList as $comment){
            $newComment = new Comment(
                new UUID($comment['uuid']),
                $post = $postsRepository->get(new UUID($comment['post_uuid'])),
                $user = $userRepository->get(new UUID($comment['author_uuid'])),
                $comment['text']
            );
            $comments[] = $newComment;
        }
        return $comments;
    }
}