<?php

namespace BLog\Repositories\CommentsRepositories;
use Blog\Comment\Comment;
use Blog\DummyLogger\DummyLogger;
use Blog\Exceptions\CommentNotFoundException;
use Blog\Post\Post;
use Blog\User\User;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;
use Person\Name;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $repository->get(new UUID('72746c50-d2c6-47c3-bd34-6772d8d053da'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '72746c50-d2c6-47c3-bd34-6772d8d053da',
                ':post_uuid' => 'd9e7fe3e-cc71-4fe1-bff0-7b54cf4ffca7',
                ':author_uuid' => '6e6b22bc-5ea1-4cda-b7fd-88a55ffc18ee',
                ':text' => 'Comment text'
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub,  new DummyLogger);

        $user = new User(
            new UUID('6e6b22bc-5ea1-4cda-b7fd-88a55ffc18ee'),
            new Name('Ivan', 'Ivanov'),
            'ivan123'
        );

        $post = new Post(
            new UUID('d9e7fe3e-cc71-4fe1-bff0-7b54cf4ffca7'),
            $user,
            'Title',
            'Some text'
        );

        $commentRepository->save(
            new Comment(
                new UUID('72746c50-d2c6-47c3-bd34-6772d8d053da'),
                $post,
                $user,
                'Comment text'
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '72746c50-d2c6-47c3-bd34-6772d8d053da',
            'post_uuid' => 'd9e7fe3e-cc71-4fe1-bff0-7b54cf4ffca7',
            'author_uuid' => '6e6b22bc-5ea1-4cda-b7fd-88a55ffc18ee',
            'text' => 'Some text here',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
            'title' => 'Post title',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentsRepository = new SqliteCommentsRepository($connectionStub,  new DummyLogger);
        $comment = $commentsRepository->get(new UUID('72746c50-d2c6-47c3-bd34-6772d8d053da'));

        $this->assertSame('72746c50-d2c6-47c3-bd34-6772d8d053da', (string) $comment->getUuid());

    }
}