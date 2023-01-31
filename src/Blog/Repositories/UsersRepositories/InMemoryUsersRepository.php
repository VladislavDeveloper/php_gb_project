<?php
namespace Blog\Repositories\UsersRepository;

use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Blog\Exceptions\UserNotFoundException;
class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];
    public function save(User $user): void
    {
        $this->users[] = $user;
    }
    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
        if ((string)$user->uuid() === (string)$uuid) {
            return $user;
        }
    }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        foreach($this->users as $user){
            if($user->username() === $username){
                return $user;
            }
        }

        throw new UserNotFoundException("User not found: $username");
    }
}
