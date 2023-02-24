<?php

namespace Blog\Commands\Posts;
use Blog\Exceptions\PostNotFoundException;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\UUID\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ){
        parent::__construct();
    }

    // Конфигурируем команду
    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete'
            )
            //Добавляем орпцию
            ->addOption(
                'check-existance',
                'c',
                InputOption::VALUE_NONE,
                'Check if posts actually exists',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $question = new ConfirmationQuestion(
            // Вопрос для подтверждения
            'Delete post [Y/n]? ',
            // По умолчанию не удалять
            false
        );
        // Ожидаем подтверждения
        if (!$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {
        // Выходим, если удаление не подтверждено
            return Command::SUCCESS;
        }
        // Получаем UUID статьи
        $uuid = new UUID($input->getArgument('uuid'));

        //Проверка существования поста

        if($input->getOption('check-existance')){
            try{
                $this->postsRepository->get($uuid);
            }catch(PostNotFoundException $error){
                $output->writeln($error->getMessage());
                return Command::FAILURE;
            }
        }

        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);

        $output->writeln("Post $uuid deleted");

            return Command::SUCCESS;

    }

}