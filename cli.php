<?php
use Comment\Comment;
use Post\Post;
use User\User;

require_once __DIR__ . '/vendor/autoload.php';
// require_once __DIR__ . '/autoload/autoload.php';

$faker = Faker\Factory::create();

switch ($argv[1]) {
    case 'user':
        $user = new User($faker->randomNumber(), $faker->firstName, $faker->lastName());
        echo $user->__toString();
        break;
    case 'post':
        $post = new Post($faker->randomNumber(), $faker->randomNumber(), $faker->title(), $faker->text());
        echo $post->__toString();
    case 'comment':
        $comment = new Comment($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->text());
        echo $comment->__toString();
}