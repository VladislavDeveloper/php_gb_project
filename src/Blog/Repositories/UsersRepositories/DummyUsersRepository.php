<?php

namespace Blog\Repositories\UsersRepositories;

use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        // Ничего не делаем
    }
    public function get(UUID $uuid): User
    {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found");
    }
    public function getByUsername(string $username): User
    {
        // Нас интересует реализация только этого метода
        // Для нашего теста не важно, что это будет за пользователь,
        // поэтому возвращаем совершенно произвольного
        return new User(UUID::random(), new Name("first", "last"), "user123");
    }
}
