<?php

namespace Blog\Repositories\UsersRepositories;
use Blog\Exceptions\UserNotFoundException;
use Blog\User\User;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;
use Person\Name;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    //Тест проверяет, что SQLite репозиторий бросает исключение, если запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot get user: Ivan !');

        $repository->getByUsername('Ivan');
    }

    //Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Ivanov',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepository($connectionStub);

        $repository->save(
            new User(
                new UUID(('123e4567-e89b-12d3-a456-426614174000')),
                new Name('Ivan', 'Ivanov'),
                'ivan123'
            )
        );
    }

    public function testItReturnsUserByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
            'username' => '123ivan',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $userRepository = new SqliteUsersRepository($connectionStub);
        $user = $userRepository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string) $user->uuid());
    }
}