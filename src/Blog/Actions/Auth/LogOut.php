<?php

namespace Blog\Actions\Auth;
use Blog\Actions\ActionInterface;
use Blog\AuthToken\AuthToken;
use Blog\Exceptions\HttpException;
use Blog\Http\Auth\TokenAuthenticationInterface;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\Response;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use DateTimeImmutable;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface $authTokenRepository,
        private TokenAuthenticationInterface $authentication
    ){
    }

    public function handle(Request $request): Response
    {
        //Проверяем авторизацию по токену и получаем сам токен
        try{
            $user = $this->authentication->user($request);
            $token = $this->authentication->getToken($request);
        }catch(HttpException $error){
            return new ErrorResponse($error->getMessage());
        }

        //Создаем объект токена и передаем в качестве значения expires_on текущюю дату
        $authToken = new AuthToken(
            $token,
            $user->uuid(),
            (new DateTimeImmutable())
        );

        //Сохраняем обновленный токен в БД
        $this->authTokenRepository->save($authToken);

        //Возвращаем успешный ответ
        return new SuccessfulResponse([
            'message' => "Logout successful"
        ]);
        
    }
}