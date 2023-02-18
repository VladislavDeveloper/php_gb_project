<?php

namespace Blog\Http\Auth;


use Blog\Exceptions\AuthException;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\Auth\AuthenticationInterface;
use Blog\Http\Request;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function user(Request $request): User
    {
        //Идентификация пользователя
        try{
            $username = $request->jsonBodyField('username');
        }catch(HttpException $error){
            throw new AuthException($error->getMessage());
        }

        try{
            $user = $this->usersRepository->getByUsername($username);
        }catch(UserNotFoundException $error){
            throw new AuthException($error->getMessage());
        }

        //Аутентификация пользователя

        try{
            $password = $request->jsonBodyField('password');
        }catch(HttpException $error){
            throw new AuthException($error->getMessage());
        }

        if(!$user->checkPassword($password)){
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}