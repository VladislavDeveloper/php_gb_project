<?php

namespace Blog\Actions\Comments;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class AddComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try{
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        }catch(HttpException | InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        try{
            $post = $this->postsRepository->get($postUuid);
            $user = $this->usersRepository->get($authorUuid);
        }catch(UserNotFoundException | PostNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        $commentUuid = UUID::random();

        try{
            $comment = new \Blog\Comment\Comment(
                $commentUuid,
                $post,
                $user,
                $request->jsonBodyField('text')
            );
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string) $commentUuid
        ]);
    }
}