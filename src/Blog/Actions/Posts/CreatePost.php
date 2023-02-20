<?php

namespace Blog\Actions\Posts;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Auth\AuthenticationInterface;
use Blog\Http\Auth\IdentificationInterface;
use Blog\Http\Auth\TokenAuthenticationInteface;
use Blog\Http\Auth\TokenAuthenticationInterface;
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
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    ){
    }

    public function handle(Request $request): Response
    {
        // Проверяем авторизацию пользователя по токену и получаем объект юзера
        try{
            $author = $this->authentication->user($request);
        }catch(UserNotFoundException $error){
            $this->logger->warning("Create post: User not found");
            return new ErrorResponse($error->getMessage());
        }

        //Генерируем uuid
        $newPostUuid = UUID::random();

        //Создаем объект поста
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

        //Сохраняем пост в БД
        $this->postsRrepository->save($post);

        //Возвращаем успешный ответ
        return new SuccessfulResponse([
            'uuid' => (string) $newPostUuid
        ]);
    }
}