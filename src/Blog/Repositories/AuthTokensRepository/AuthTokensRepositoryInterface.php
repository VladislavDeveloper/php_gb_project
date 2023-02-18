<?php

namespace Blog\Repositories\AuthTokensRepository;
use Blog\AuthToken\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}