<?php

namespace Blog\Actions\Posts;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class DropPost
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ){
    }

    public function handle(Request $requset): Response
    {
        try{
            $uuid = new UUID($requset->query('uuid'));
        }catch(HttpException | InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        try{
            $post = $this->postsRepository->get($uuid);
        }catch(PostNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRepository->deletePost($uuid);

        return new SuccessfulResponse([
            'message' => 'Post deleted sccessfully'
        ]);
    }
}