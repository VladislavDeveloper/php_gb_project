<?php

namespace Blog\Actions\Posts;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;

class FindByUuid implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {
    }
    
    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('post_uuid');
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $post->getTitle(),
            'text' => $post->getText(),
            'author' => $post->getUser()->__toString()
        ]);
    }
}