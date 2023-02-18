<?php

namespace Blog\Commands;

use Blog\DummyLogger\DummyLogger;
use Blog\Exceptions\ArgumentsException;
use Blog\Exceptions\CommandException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\DummyUsersRepository;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Type\VoidType;

class CreateUserCommandsTest extends TestCase
{

    private function makeDummyUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface{
            public function save(User $user): void
            {
                //Ничего
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
    
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeDummyUsersRepository(), new DummyLogger);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');

        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeDummyUsersRepository(), new DummyLogger);

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
        ]));
    }

    //Тест проверяет, что команла сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface{
            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function wasCalled(): bool
            {
                return $this->called;
            }

        };

        $command = new CreateUserCommand($usersRepository, new DummyLogger);

        $command->handle(new Arguments([
            'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));

        
        $this->assertTrue($usersRepository->wasCalled());
    }

    //Проверка запроса пароля
    public function testItRequiersPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeDummyUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');

        $command->handle(
            new Arguments([
                'username' => 'Vasija',
                'first_name' => 'Vasija',
                'last_name' => 'Ivanov'
            ])
        );
    }
}