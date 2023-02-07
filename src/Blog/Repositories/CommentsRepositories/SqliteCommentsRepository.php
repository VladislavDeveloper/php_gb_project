<?php

namespace Blog\Repositories\CommentsRepositories;
use Blog\Comment\Comment;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\UUID\UUID;
use PDO;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPost()->getUuid(),
            ':author_uuid' => $comment->getAuthor()->uuid(),
            ':text' => $comment->getText(),
        ]);
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

        $postsRepository = new SqlitePostsRepository($this->connection);

        $post = $postsRepository->get(new UUID($result['post_uuid']));

        $userRepository = new SqliteUsersRepository($this->connection);

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

        $postsRepository = new SqlitePostsRepository($this->connection);

        $userRepository = new SqliteUsersRepository($this->connection);

        $commentsList = $statement->fetchAll();

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