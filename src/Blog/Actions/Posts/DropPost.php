<?php

namespace Blog\Actions\Posts;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Auth\TokenAuthenticationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use InvalidArgumentException;

class DropPost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication
    ){
    }

    public function handle(Request $requset): Response
    {

        //Проверяем авторизацию пользователя по токену
        try{
            $author = $this->authentication->user($requset);
        }catch(UserNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }
        
        //Получаем uuid из params запроса 
        try{
            $uuid = new UUID($requset->query('uuid'));
        }catch(HttpException | InvalidArgumentException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Получаем пост по uuid
        try{
            $post = $this->postsRepository->get($uuid);
        }catch(PostNotFoundException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Проверяем что пользователя который хочет удалить пост является автором этого поста
        if($post->getUser()->uuid() != $author->uuid()){
            return new ErrorResponse('Permission danied');
        }

        //Удаляем пост
        $this->postsRepository->delete($uuid);

        return new SuccessfulResponse([
            'message' => "Post: $uuid deleted"
        ]);
    }
}