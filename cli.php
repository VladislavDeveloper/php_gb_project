<?php
use Blog\Commands\Arguments;
use Blog\Commands\CreateUserCommand;
use Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

$logger = $container->get(LoggerInterface::class);

try{
    $command->handle(Arguments::fromArgv($argv));
}catch(AppException $error){
    $logger->error($error->getMessage(), ['exception' => $error]);
}