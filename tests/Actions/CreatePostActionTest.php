<?php

namespace Blog\Actions\Posts;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotFoundException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\SuccessfulResponse;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use PHPUnit\Framework\TestCase;

class CreatePostTestTest extends TestCase
{
    private function postsRepository(): PostsRepositoryInterface{
        return new class() implements PostsRepositoryInterface{
            private bool $called = false;

            public function __construct()
            {}

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not Found');
            }

            public function deletePost(UUID $uuid): void
            {
            }

            public function getCalled(): bool
            {
                return $this->called;
            }
        };
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface
        {
            public function __construct(
                private array $users
            )
            {
            }

            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                foreach($this->users as $user){
                    if($user instanceof User && (string)$uuid == $user->uuid()){
                        return $user;
                    }
                }
                throw new UserNotFoundException('Cannot find user: ' . $uuid);
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('Not Found');
            }
        };
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid":"e0f29ef4-39e8-4d4d-b8f2-defd537f5915",
        "title":"title", "text":"text" }');

        $postsRepository = $this->postsRepository();

        $usersRepository = $this->usersRepository([
            new User(
                new UUID('e0f29ef4-39e8-4d4d-b8f2-defd537f5915'),
                new Name('Ivan', 'Ivanov'),
                'user123'
            )
        ]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data) {
            $data_decode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );
            $data_decode['data']['uuid'] = "e0f29ef4-39e8-4d4d-b8f2-defd537f5915";
            return json_encode(
                $data_decode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString('{"SUCCESS":true,"data":{"uuid":"e0f29ef4-39e8-4d4d-b8f2-defd537f5915"}}');

        $response->send();
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNotFoundUser(): void
    {
        $request = new Request([], [], '{"author_uuid":"e0f29ef4-39e8-4d4d-b8f2-defd537f5915",
             "title":"title", "text":"text"}');

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"SUCCESS":false,"reason":"Cannot find user: e0f29ef4-39e8-4d4d-b8f2-defd537f5915"}');

        $response->send();
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    */
    public function testItReturnsErrorResponseIfNoTextProvided(): void
    {
        $request = new Request([], [], '{"author_uuid":"e0f29ef4-39e8-4d4d-b8f2-defd537f5915",
            "title":"title"}');

        $postsRepository = $this->postsRepository();
        $usersRepository = $this->usersRepository([
            new User(
                new UUID('e0f29ef4-39e8-4d4d-b8f2-defd537f5915'),
                new Name('Ivan', 'Ivanov'),
                'user123'
            )
        ]);

        $action = new CreatePost($postsRepository, $usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"SUCCESS":false,"reason":"No such field: text"}');

        $response->send();
    }

}