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

        if($this->userExists($username)){
            $this->logger->warning("User already exists: $username");
            return;
        }

        $uuid = UUID::random();

        $this->usersRepository->save(
            new User(
                $uuid,
                new Name($arguments->get('first_name'), $arguments->get('last_name')),
                (string) $username
            )
        );

        $this->logger->info("User created: $uuid");
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