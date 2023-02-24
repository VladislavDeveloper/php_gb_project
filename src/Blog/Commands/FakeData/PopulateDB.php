<?php

namespace Blog\Commands\FakeData;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int{
        $users = [];
        for($i = 0; $i< 10; $i++){
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        foreach ($users as $user){
            for($i = 0; $i < 5; $i++){
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->userName,
            new Name(
            // Генерируем имя
                $this->faker->firstName,
                // Генерируем фамилию
                $this->faker->lastName
            ),
            $this->faker->password,
        );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        return $user;
    }


    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }
}