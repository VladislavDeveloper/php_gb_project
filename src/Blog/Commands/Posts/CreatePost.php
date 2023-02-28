<?php

namespace Blog\Commands\Posts;
use Blog\Exceptions\UserNotFoundException;
use Blog\Post\Post;
use Blog\Repositories\PostsRepositories\PostsRepositoryInterface;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Blog\UUID\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePost extends Command
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setName('posts:create')
        ->setDescription('Create new post')
        ->addArgument('username', InputArgument::REQUIRED, "Author's username")
        ->addArgument('title', InputArgument::REQUIRED, "Post's title")
        ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ):int
    {
        $output->writeln('Create post command started');

        $username = $input->getArgument('username');

        $user = $this->userExists($username);

        if(!$user){
            $output->writeln("User not found: $username");
            return Command::FAILURE;
        }

        $post = new Post(
            UUID::random(),
            $user,
            $input->getArgument('title'),
            $input->getArgument('text')
        );

        $this->postsRepository->save($post);

        $output->writeln('Post saved: ' . $post->getUuid());

        return Command::SUCCESS;
    }

    private function userExists(string $username): ?User
    {
        try{
            $user = $this->usersRepository->getByUsername($username);
        }catch(UserNotFoundException){
            return null;
        }

        return $user;
    }
}