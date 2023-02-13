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
use Blog\UUID\UUID;
use Person\Name;

class CreateNewUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try {
            $username = $request->jsonBodyField('username');
            $firstName = $request->jsonBodyField('first_name');
            $lastName = $request->jsonBodyField('last_name');
        } catch (HttpException $error) {
            return new ErrorResponse($error->getMessage());
        }

        try{
            $user = new User(
                $uuid = new UUID(UUID::random()),
                new Name($firstName, $lastName),
                $username
            );
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string) $uuid
        ]);

    }
}