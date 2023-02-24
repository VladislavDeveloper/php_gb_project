<?php

namespace Blog\Actions\Comments;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Auth\TokenAuthenticationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class AddComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication
    ){
    }

    public function handle(Request $request): Response
    {
        //Проверяем авторизацию пользователя по токену
        try{
            $user = $this->authentication->user($request);
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        // Получаем post_uuid из запроса 
        try{
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        }catch(InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Получаем посто по post_uuid
        try{
            $post = $this->postsRepository->get($postUuid);
        }catch(PostNotFoundException $error){
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

        //Сохраняем комментарий в БД
        $this->commentsRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string) $commentUuid
        ]);
    }
}