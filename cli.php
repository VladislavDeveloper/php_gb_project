<?php

use Blog\Commands\Arguments;
use Blog\Commands\CreateUserCommand;
use Blog\Comment\Comment;
use Blog\Repositories\CommentsRepositories\SqliteCommentsRepository;
use Blog\Repositories\PostsRepositories\SqlitePostsRepository;
use Blog\Repositories\UsersRepositories\SqliteUsersRepository;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use Blog\Post\Post;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.db');

$usersRepository = new SqliteUsersRepository($connection);

$postsRepository = new SqlitePostsRepository($connection);

$commentsRepository = new SqliteCommentsRepository($connection);

$command = new CreateUserCommand($usersRepository);

$user = $usersRepository->get(new UUID('1cc3258e-550a-4b25-98d5-054ed1ce1529'));

$user2 = $usersRepository->get(new UUID('6e6b22bc-5ea1-4cda-b7fd-88a55ffc18ee'));

//Создание нового пользователя
// $usersRepository->save(new User(UUID::random(), new Name('Vasija', 'Doe'), 'Vasija600'));


try {

    //Обработка команды создания пользователя через терминал
    $command->handle(Arguments::fromArgv($argv));

    //Создание поста
    // $post = new Post(UUID::random(), $user, 'Заголовок', 'Текст поста');
    // $postsRepository->save($post);

    //Получение поста по uuid
    // $post = $postsRepository->get(new UUID('845b3729-14c3-442b-8754-f99eedc221f4'));
    // print_r($post);

    //Создание комментария нового поста и комментария к нему от имени другого
    // $post = new Post(UUID::random(), $user, 'Заголовок2', 'Какой-то текст');
    // $postsRepository->save($post);
    // $commentsRepository->save(
    //     new Comment(UUID::random(), $post, $user2, 'Интересный пост !')
    // );

    //Получение объекта комментария по uuid
    // $comment = $commentsRepository->get(new UUID('72746c50-d2c6-47c3-bd34-6772d8d053da'));
    // print_r($comment);

    //Получение массива комментариев к посту по post_uuid
    // $comments = $commentsRepository->getCommentsByPost(new UUID('d9e7fe3e-cc71-4fe1-bff0-7b54cf4ffca7'));
    // print_r($comments);

    //Получение массива постов пользователя по author_uuid
    // $posts = $postsRepository->getPostsByUser(new UUID('1cc3258e-550a-4b25-98d5-054ed1ce1529'));
    // print_r($posts);


} catch (Exception $e) {
    echo $e->getMessage();
}