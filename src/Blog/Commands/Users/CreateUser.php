<?php

namespace Blog\Commands\Users;
use Blog\Exceptions\UserNotFoundException;
use Blog\Repositories\UsersRepositories\UsersRepositoryInterface;
use Blog\User\User;
use Person\Name;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
        parent::__construct(); 
    }

    protected function configure(): void
    {
        $this
        ->setName('users:create')
        ->setDescription('Create new user')
        ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
        ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
        ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute( 
        InputInterface $input,
        OutputInterface $output, 
    ): int
    {
        $output->writeln('Create user command started');

        $username = $input->getArgument('username');

        if($this->userExists($username)){
            $output->writeln("User already exists: $username");

            return Command::FAILURE;
        }

        $user = User::createFrom(
            $username,
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name')
            ),
            $input->getArgument('password')
        );

        $this->usersRepository->save($user);

        $output->writeln('User saved: ' . $user->uuid());

        return Command::SUCCESS;
    }

    private function userExists(string $username): bool
    {
        try{
            $this->usersRepository->getByUsername($username);
        }catch(UserNotFoundException){
            return false;
        }

        return true;
    }
}