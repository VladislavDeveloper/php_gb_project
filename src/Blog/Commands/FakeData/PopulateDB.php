<?php

namespace Blog\Commands\FakeData;
use Blog\Comment\Comment;
use Blog\Post\Post;
use Blog\Repositories\CommentsRepositories\CommentsRepositoryInterface;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class PopulateDB extends Command
{
    public function __construct(
        private \Faker\Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentRepository,
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            //Добовляем наобходимые опции - users-numbaers и posts-numbers
            ->addOption(
               'users-numbers',
               'u',
               InputOption::VALUE_OPTIONAL,
               'Users numbers' 
            )
            ->addOption(
                'posts-numbers',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts numbers'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int{

        //Получаем значения опций
        //Если опции не переданы значение счетчиков по умолчанию будет 1
        $usersNumbers = $input->getOption('users-numbers') ? : 1;
        $postsNumbers = $input->getOption('posts-numbers') ? : 1;

        $users = [];

        //Теперь в счетчиках цикла используем данные переданные через опции
        for($i = 0; $i< $usersNumbers; $i++){
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        foreach ($users as $user){
            for($i = 0; $i < $postsNumbers; $i++){
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
                //Добавление комментария к посту, по умолчанию будем добавлять по 3 комментария
                for($j = 0; $j < 3; $j++){
                    $comment = $this->createCommentToPost($post, $user);
                    $output->writeln('Comment saved: ' . $comment->getText());
                }
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
    
    //Метод для генерации комментария
    private function createCommentToPost(Post $post, User $user): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $post,
            $user,
            $this->faker->sentence(10, true)
        );

        //Сохраняем комментарий в БД
        $this->commentRepository->save($comment);
        return $comment;
    }
}