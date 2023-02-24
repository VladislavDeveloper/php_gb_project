<?php

namespace Blog\Actions\Users;
use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Person\Name;


class CreateNewUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ){
    }

    public function handle(Request $request): Response
    {

        //Получаем данные для регистрации нового пользователя из тела запроса
        try {
            $username = $request->jsonBodyField('username');
            $firstName = $request->jsonBodyField('first_name');
            $lastName = $request->jsonBodyField('last_name');
            $password = $request->jsonBodyField('password');
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        //Создаем объект пользователя
        $user = User::createFrom(
            $username,
            new Name(
                $firstName,
                $lastName
            ),
            $password
        );

        //Сохраняем пользователя в БД
        try{
            $this->usersRepository->save($user);
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }
        
        //Возвращаем успешный ответ
        return new SuccessfulResponse([
            'uuid' => (string) $user->uuid()
        ]);

    }
}