<?php

namespace Blog\Repositories\LikesRepository;

use Blog\Exceptions\AlreadyLikedException;
use Blog\Exceptions\LikesNotFoundException;
use Blog\Like\Like;
use Blog\UUID\UUID;
use PDO;
use Psr\Log\LoggerInterface;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid)
            VALUES (:uuid, :post_uuid, :author_uuid)'
        );

        $likedPost = $like->getPostUuid();

        $likeAuthor = $like->getAuthorUuid();

        $statement->execute([
            ':uuid' => (string) $like->getUuid(),
            ':post_uuid' => (string)  $likedPost,
            ':author_uuid' => (string) $likeAuthor
        ]);

        //Логируем сообщение о том, что такой-то пост лайкнул такой-то пользователь
        $this->logger->info("Post $likedPost liked by user  $likeAuthor");
    }

    public function getByPostUuid(UUID $post_uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => $post_uuid
        ]);

        $likesList = $statement->fetchAll();

        if(!$likesList){
            $this->logger->warning("No likes to post: $post_uuid");

            throw new LikesNotFoundException('No likes to post');
        }

        $likes = [];

        foreach($likesList as $likeItem){
            $like = new Like(
                new UUID($likeItem['uuid']),
                new UUID($likeItem['post_uuid']),
                new UUID($likeItem['author_uuid']),
            );
            $likes[] = $like;
        }

        return $likes;
    }

    public function checkUserAlreadyLikedPost($post_uuid, $author_uuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid AND author_uuid = :author_uuid'
        );

        $statement->execute([
            ':post_uuid' => $post_uuid,
            ':author_uuid' => $author_uuid
        ]);

        $existed = $statement->fetch();

        if($existed){
            $this->logger->warning("This user already liked this post: $post_uuid");
            
            throw new AlreadyLikedException('This user already liked this post');
        }
    }
}