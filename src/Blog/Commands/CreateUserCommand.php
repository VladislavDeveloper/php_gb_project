<?php

namespace Blog\Commands;

use Blog\Exceptions\CommandException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if($this->userExists($username)){
            throw new CommandException("User already exists: $username");
        }

        $this->usersRepository->save(
            new User(
                UUID::random(),
                new Name($arguments->get('first_name'), $arguments->get('last_name')),
                (string) $username
            )
        );
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }   
}