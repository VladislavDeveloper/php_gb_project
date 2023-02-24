<?php

namespace Blog\Repositories\PostsRepositories;
use Blog\DummyLogger\DummyLogger;
use Blog\Exceptions\PostNotFoundException;
use Blog\Post\Post;
use Blog\User\User;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;
use Person\Name;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{

    //Тест проверяет что команда бросает исключенеи в случае, когда пост не найден
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub,  new DummyLogger);

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: $uuid');

        $repository->get(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }

    //Тест проверяет что команда сохраняет пост в БД
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Some title',
                ':text' => 'Some text here',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub,  new DummyLogger);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('Ivan', 'Ivanov'),
            'ivan123',
            'qwerty'
        );

        $repository->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Some title',
                'Some text here'
            )
        );
    }

    //Тест проверяет что команда получает пост по его uuid
    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '2bfe4536-87bc-406b-b417-07f316a12d5d',
            'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'Some title',
            'text' => 'Some text here',
            'username' => 'ivan123',
            'password' => 'qwerty',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub,  new DummyLogger);
        $post = $postRepository->get(new UUID('2bfe4536-87bc-406b-b417-07f316a12d5d'));

        $this->assertSame('2bfe4536-87bc-406b-b417-07f316a12d5d', (string) $post->getUuid());
    }

}