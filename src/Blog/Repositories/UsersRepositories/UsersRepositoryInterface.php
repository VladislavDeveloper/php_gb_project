<?php

namespace Blog\Repositories\UsersRepositories;
use Blog\User\User;
use Blog\UUID\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}