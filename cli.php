<?php
use Blog\Commands\Arguments;
use Blog\Commands\CreateUserCommand;
use Blog\Exceptions\AppException;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try{
    $command->handle(Arguments::fromArgv($argv));
}catch(AppException $error){
    echo "{$error->getMessage()} \n";
}