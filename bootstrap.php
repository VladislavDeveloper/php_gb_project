<?php
use Blog\Container\DIContainer;
use Blog\Http\Auth\IdentificationInterface;
use Blog\Http\Auth\JsonBodyUsernameIdentification;
use Blog\Http\Auth\JsonBodyUuidIdentification;
use Blog\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

//Подключаем переменные окружения
Dotenv::createImmutable(__DIR__)->safeLoad();

//Создаем экземпляр контейнера
$container = new DIContainer();

//Подключение к БД

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . $_ENV['PATH_TO_DB_SQLITE'])
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

//Репозиторий лайков
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

//Подключаем контракт с реализации идентификации в контейнер
$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);

//Подключаем логгер в контейнер зависимостей приложения

$logger = (new Logger('logbook'));

//Вместо проверки переменных окружения на значение "yes", сделал проверку на true
if($_ENV['LOG_TO_FILES'] == "true"){
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/logbook.log'
    ))
    ->pushHandler(new StreamHandler(
        __DIR__ . '/logs/logbook.errors.log',
        level: Logger::ERROR,
        bubble: false
    ));
}

//Аналогично включаем логирование в консоль если переменная окружения LOG_TO_CONSOLE == true

if($_ENV['LOG_TO_CONSOLE'] == "true"){
    $logger->pushHandler(new StreamHandler("php://stdout"));
}

$container->bind(
    LoggerInterface::class,
    $logger
);

//Возвращаем объект контейнера
return $container;