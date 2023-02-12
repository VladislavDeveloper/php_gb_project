<?php

namespace Blog\Actions\Posts;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRrepository,
        private UsersRepositoryInterface $usersRrepository,
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        }catch(HttpException | InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        try{
            $user = $this->usersRrepository->get($authorUuid);
        }catch(UserNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Генерируем uuid

        $newPostUuid = UUID::random();

        try{
            $post = new Post(
                $newPostUuid,
                $user,
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