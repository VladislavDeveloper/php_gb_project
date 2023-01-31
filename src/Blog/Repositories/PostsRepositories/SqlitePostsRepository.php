<?php

namespace Blog\Repositories\PostsRepositories;

use Blog\Post\Post;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

	public function save(Post $post): void 
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string) $post->getUuid(),
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);
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

        $userRepository = new SqliteUsersRepository($this->connection);

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

        $userRepository = new SqliteUsersRepository($this->connection);

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
}