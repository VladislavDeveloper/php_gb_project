<?php

namespace Blog\Repositories\PostsRepositories;

use Blog\Exceptions\PostNotFoundException;
use Blog\Post\Post;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    )
    {
    }

	public function save(Post $post): void 
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $postUuid = $post->getUuid();

        $statement->execute([
            ':uuid' => (string) $postUuid,
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

        //Логируем сообщение о создании поста
        $this->logger->info("Post saved: $postUuid");
	}
	
	public function get(UUID $uuid): Post 
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

        return $this->getPost($statement, $uuid);
	}

    private function getPost(PDOStatement $statement, $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result === false){

            //Логируем сообщение о том что пост не найден с уровнем warning а затем бросаем исключение
            $this->logger->warning("Post not found: $uuid");

            throw new PostNotFoundException(
                'Cannot find post: $uuid'
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);

        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text'],
        );
    }

    public function getPostsByUser(UUID $author_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE author_uuid = :author_uuid'
        );

        $statement->execute([
            ':author_uuid' => $author_uuid
        ]);

        $postsList = $statement->fetchAll();

        if(!$postsList){
            $this->logger->warning("User $author_uuid has no posts yet");

            throw new PostNotFoundException(
                'Cannot find post: $uuid'
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);

        $posts = [];

        foreach($postsList as $post){
            $newPost = new Post(
                new UUID($post['uuid']),
                $user = $userRepository->get(new UUID($author_uuid)),
                $post['title'],
                $post['text']
            );
            $posts[] = $newPost;
        }
        return $posts;
    }

    //Метод для удаления статьи по ее uuid
    public function deletePost(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

        $this->logger->info("Post deleted:  $uuid");
    }
}