<?php
use Blog\Container\DIContainer;
use Blog\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

//Создаем экземпляр контейнера
$container = new DIContainer();

//Подключение к БД

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.db')
);

//Репозиторий постов

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

//Ропозиторий пользователей

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

//Ропизиторий комментариев

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

//Возвращаем объект контейнера
return $container;