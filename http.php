<?php
use Blog\Actions\Comments\AddComment;
use Blog\Actions\Posts\CreatePost;
use Blog\Actions\Posts\DropPost;
use Blog\Actions\Users\FindByUsername;
use Blog\Exceptions\AppException;
use Blog\Exceptions\HttpException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;
use Blog\Http\SuccessfulResponse;
use Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET, 
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try{
    $method = $request->method();
}catch(HttpException){
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
                new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
                new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            ),
                new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            )
        ),
        '/comments/add' => new AddComment(
            new SqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            ),
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            )
        )
    ],
    'DELETE' => [
        '/posts/drop' => new DropPost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.db')
            )
        )
    ]
];

if(!array_key_exists($method, $routes)){
    (new ErrorResponse('Not found'))->send();
    return;
}

if(!array_key_exists($path, $routes[$method])){
    (new ErrorResponse("Not found $method"))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (AppException $error) {
    (new ErrorResponse($error->getMessage()))->send();
}

$response->send();