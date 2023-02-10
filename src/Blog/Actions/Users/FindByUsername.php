<?php

namespace Blog\Actions\Users;

use Blog\Actions\ActionInterface;
use Blog\Exceptions\HttpException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;

class FindByUsername implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        return new SuccessfulResponse([
            'username' => $user->getUsername(),
            'name' => $user->Name()->getFirstName() . ' ' . $user->Name()->getLastName()
        ]);
    }
}