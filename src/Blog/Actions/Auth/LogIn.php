<?php

namespace Blog\Actions\Auth;
use Blog\Actions\ActionInterface;
use Blog\AuthToken\AuthToken;
use Blog\Exceptions\AuthException;
use Blog\Http\Auth\PasswordAuthenticationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use DateTimeImmutable;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    ){
    }

    public function handle(Request $request): Response
    {
        //Проверяем аутентификацию по паролю
        try{
            $user = $this->passwordAuthentication->user($request);
        }catch(AuthException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Генерируем токен
        $authToken = new AuthToken(
            //Генерируем случайную строку в 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new DateTimeImmutable())->modify('+1 day')
        );

        //Сохраняем токен в БД
        $this->authTokensRepository->save($authToken);

        //Возвращаем успешный ответ
        return new SuccessfulResponse([
            'token' => (string)$authToken->token()
        ]);
    }
}



