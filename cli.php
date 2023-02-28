<?php
use Blog\Commands\Arguments;
use Blog\Commands\CreateUserCommand;
use Blog\Commands\FakeData\PopulateDB;
use Blog\Commands\Posts\CreatePost;
use Blog\Commands\Posts\DeletePost;
use Blog\Commands\Users\CreateUser;
use Blog\Commands\Users\UpdateUser;
use Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

//Создаем объект приложения
$application = new Application();

//Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
    CreatePost::class
];

foreach($commandsClasses as $commandClass){
    $command = $container->get($commandClass);

    $application->add($command);
}

//Запуск приложения
$application->run();