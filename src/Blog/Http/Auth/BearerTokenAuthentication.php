<?php

namespace Blog\Http\Auth;
use Blog\Exceptions\AuthException;
use Blog\Exceptions\AuthTokenNotFoundException;
use Blog\Exceptions\HttpException;
use Blog\Http\Request;
use Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use DateTimeImmutable;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UsersRepositoryInterface $usersRepository
    ){
    }

    public function user(Request $request): User
    {
        try{
            $header = $request->header('Authorization');
        }catch(HttpException $error){
            throw new AuthException($error->getMessage());
        }

        //Проверяем формат заголовка
        if(!str_starts_with($header, self::HEADER_PREFIX)){
            throw new AuthException("Malformed token: [$header]");
        }

        //Убираем префикс
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));

        try{
            $authToken = $this->authTokensRepository->get($token);
        }catch(AuthTokenNotFoundException){
            throw new AuthException("Bad token: $token");
        }

        //Проверяем срок годности токена
        if($authToken->expiresOn() <= new DateTimeImmutable()){
            throw new AuthException("Token expired: $token");
        }

        $userUuid = $authToken->userUuid();

        return $this->usersRepository->get($userUuid);

    }
}