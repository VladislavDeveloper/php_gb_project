<?php

namespace Blog\Commands;

use Blog\Commands\Users\CreateUser;
use Blog\DummyLogger\DummyLogger;
use Blog\Exceptions\ArgumentsException;
use Blog\Exceptions\CommandException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\DummyUsersRepository;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SebastianBergmann\Type\VoidType;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
    

    //Проверка запроса пароля
    public function testItRequiersPassword(): void
    {
        $command = new CreateUser(
            $this->makeDummyUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name, last_name, password"');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
            
    }

    public function testItRequiresLastName(): void
    {
        $command = new CreateUser(
            $this->makeDummyUsersRepository(),
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage('Not enough arguments (missing: "first_name, last_name").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'qwerty',
                'first_name'
            ]),
            //Передаём также объект, реализующий контракт OutputInterface
            new NullOutput()
        );
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeDummyUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "first_name, last_name").');

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
            
    }

    //Тест проверяет, что команла сохраняет пользователя в репозитори
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

        $command = new CreateUser($usersRepository);

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        
        $this->assertTrue($usersRepository->wasCalled());
    }
}