<?php

namespace Blog\Actions\Likes;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\AlreadyLikedException;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Http\Auth\TokenAuthenticationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Like\Like;
use Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class AddLikeToPost implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication
    ){
    }

    public function handle(Request $request): Response
    {
        //Проверяем авторизацию по токену
        try{
            $author = $this->authentication->user($request);
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Получаем post_uuid из тела запроса
        try{
            $post_uuid = new UUID($request->jsonBodyField('post_uuid'));
        }catch(InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Находим пост по uuid
        try{
            $this->postsRepository->get(new UUID($post_uuid));
        }catch(PostNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Проверяем что пользователь ставит лайк первый раз
        try{
            $this->likesRepository->checkUserAlreadyLikedPost($post_uuid, $author->uuid());
        }catch(AlreadyLikedException $error){
            return new ErrorResponse($error->getMessage());
        } 

        //Создаем объект комментария
        try{
            $like = new Like(
                new UUID(UUID::random()),
                $post_uuid,
                $author->uuid()
            );
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Сохраняем комментарий в БД
        $this->likesRepository->save($like);

        $author_username = $author->getUsername();

        //Возвращаем успешный ответ
        return new SuccessfulResponse([
            'message' => "$author_username liked post: $post_uuid"
        ]);
    }

}