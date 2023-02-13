<?php

namespace Blog\Actions\Posts;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Auth\IdentificationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRrepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $author = $this->identification->user($request);
        }catch(UserNotFoundException $error){
            $this->logger->warning("Create post: User not found");
            return new ErrorResponse($error->getMessage());
        }

        //Генерируем uuid

        $newPostUuid = UUID::random();

        try{
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRrepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string) $newPostUuid
        ]);
    }
}