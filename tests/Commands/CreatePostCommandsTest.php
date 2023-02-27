<?php

namespace Blog\Commands;
use Blog\Commands\Posts\CreatePost;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreatePostCommandsTest extends TestCase
{
    private function makeDummyPostsRepository(): PostsRepositoryInterface
    {
        return new class implements PostsRepositoryInterface
        {
            private bool $called = false;

            public function save(Post $post): void
            {
                $this->called = true;
            }
            public function get(UUID $post): Post
            {
                throw new PostNotFoundException("Not found");
            }
            public function delete(UUID $post): void
            {
                throw new PostNotFoundException("Not found");
            }
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
    }

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

    public function testItRequiresUsername(): void
    {
        $command = new CreatePost(
            $this->makeDummyPostsRepository(),
            $this->makeDummyUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "username").');

        $command->run(
            new ArrayInput([
                'title' => 'My title',
                'text' => 'Some text'
            ]),
            new NullOutput()
        );

    }
    public function testItRequiresTitle(): void
    {
        $command = new CreatePost(
            $this->makeDummyPostsRepository(),
            $this->makeDummyUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "title").');

        $command->run(
            new ArrayInput([
                'username' => 'Vasija',
                'text' => 'Some text'
            ]),
            new NullOutput()
        );

    }
    public function testItRequiresText(): void
    {
        $command = new CreatePost(
            $this->makeDummyPostsRepository(),
            $this->makeDummyUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "text").');

        $command->run(
            new ArrayInput([
                'username' => 'Vasija',
                'title' => 'My title',
            ]),
            new NullOutput()
        );

    }

    public function testItSavesPostToRepository(): void
    {
        $postsRepository = new class implements PostsRepositoryInterface
        {
            private bool $called = false;

            public function save(Post $post): void
            {
                $this->called = true;
            }
            public function get(UUID $post): Post
            {
                throw new PostNotFoundException("Not found");
            }
            public function delete(UUID $post): void
            {
                throw new PostNotFoundException("Not found");
            }
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $usersRepository = new class implements UsersRepositoryInterface{
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
                return new User(
                    UUID::random(),
                    new Name('Vasija', 'Doe'),
                    'Vasija1234',
                    'qwerty'
                );
            }
        };

        $command = new CreatePost(
            $postsRepository,
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'username' => 'Vasija',
                'title' => 'My title',
                'text' => 'Some text',
            ]),
            new NullOutput()
        );

        $this->assertTrue($postsRepository->wasCalled());
    }
}