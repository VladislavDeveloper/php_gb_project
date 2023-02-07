<?php

//Пример автозагрузчика

spl_autoload_register(function ($class) {
    print_r($class . PHP_EOL);
    $path = 'src/' . str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class) . '.php';
    if(file_exists($path)){
        print_r($path . PHP_EOL);
        require_once $path;
    }
});