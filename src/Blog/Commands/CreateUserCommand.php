<?php

namespace Blog\Commands;

use Blog\Exceptions\CommandException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ){
    }

    public function handle(Arguments $arguments): void
    {

        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        $password = $arguments->get('password');

        if($this->userExists($username)){
            $this->logger->warning("User already exists: $username");
            return;
        }

       $user = User::createFrom(
        $username,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            ),
        $password,
        );

        $this->usersRepository->save($user);

        $this->logger->info("User created: " . $user->uuid());
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