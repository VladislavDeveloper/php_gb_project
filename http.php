<?php
use Blog\Actions\Comments\AddComment;
use Blog\Actions\Likes\AddLikeToPost;
use Blog\Actions\Posts\CreatePost;
use Blog\Actions\Posts\DropPost;
use Blog\Actions\Posts\FindByUuid;
use Blog\Actions\Users\FindByUsername;
use Blog\Exceptions\AppException;
use Blog\Exceptions\HttpException;
use Blog\Http\ErrorResponse;
use Blog\Http\Request;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try{
    $path = $request->path();
}catch(HttpException){
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
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/comments/add' => AddComment::class,
        '/posts/like/add' => AddLikeToPost::class
    ],
    'DELETE' => [
        '/posts/drop' => DropPost::class,
    ]
];

if(!array_key_exists($method, $routes)){
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

if(!array_key_exists($path, $routes[$method])){
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

$actionName = $routes[$method][$path];

$action = $container->get($actionName);

try{
    $response = $action->handle($request);
}catch(AppException $error){
    (new ErrorResponse($error->getMessage()))->send();
}

$response->send();