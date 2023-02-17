<?php
namespace Blog\Repositories\UsersRepositories;

use Blog\Exceptions\UserNotFoundException;
use Blog\User\User;
use Blog\UUID\UUID;
use PDO;
use PDOStatement;
use Person\Name;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
            VALUES (:uuid, :username, :first_name, :last_name)'
        );

        $uuid = $user->uuid();

        $statement->execute([
            ':uuid' => (string) $uuid,
            ':username' => $user->getUsername(),
            ':first_name' => $user->Name()->getFirstName(),
            ':last_name' => $user->Name()->getLastName(),
        ]);

        //Логируем сообщении об успешном сохранении пользователя в БД
        $this->logger->info("User saved: $uuid");
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);

    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);

    }

    private function getUser(PDOStatement $statement, string $errorString): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if(false === $result){
            //Логируем сообщение о том что пользователь не найден с уровнем warning а затем бросаем исключение
            $this->logger->warning("User not found: $errorString");

            throw new UserNotFoundException(
                "Cannot get user: $errorString !",
            );
        }

        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']
        );
    }
}