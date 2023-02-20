<?php

namespace Blog\Actions;

use Blog\Exceptions\UserNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Blog\Actions\Users\FindByUsername;
use Person\Name;
use PHPUnit\Framework\TestCase;


class FindByUsernameActionTest extends TestCase
{
    //Запускаем тест в отдельном процессе

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        //Создаем объект запросаа
        $request = new Request([], [], '');
        //Создаем стаб репозитория
        $usersRepository = $this->usersRepository([]);
        //Создаем объект действия
        $action = new FindByUsername($usersRepository);
        //Запускаем действие
        $response = $action->handle($request);

        //Проверяем, что ответ неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);

        //Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString('{"SUCCESS":false,"reason":"No such query param in the request: $param"}');

        $response->send();

    }
    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['username' => 'Ivan'], [], '');

        $usersRepository = $this->usersRepository([]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"SUCCESS":false,"reason":"Not found"}');

        $response->send();
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'user123'], [], '');

        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Ivanov'),
                'user123',
                'qwerty'
            )
        ]);

        $action = new FindByUsername($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->expectOutputString('{"SUCCESS":true,"data":{"username":"user123","name":"Ivan Ivanov"}}');

        $response->send();
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface
        {
            public function __construct(
                private array $users
            ){
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach($this->users as $user){
                    if($user instanceof User && $username === $user->getUsername()){
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }

}