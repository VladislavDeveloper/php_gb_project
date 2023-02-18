<?php

namespace Blog\User;

use Blog\UUID\UUID;
use Person\Name;
class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $username,
        private string $hashedPassword
    ){
    }

    public function __toString(): string
    {
        return "Пользователь $this->username $this->name";
    }

    //Получение хэша пароля
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    //Проверка пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid());
    }

    //Вычисление хэша
    public static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $password . $uuid);
    }

    //Функция создания нового пользователя
    public static function createFrom(
        string $username,
        Name $name,
        string $password
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid, 
            $name,
            $username,
            self::hash($password, $uuid),
        );
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function Name()
    {
        return $this->name;
    }
 
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
            return $this->username;
    }
    /**
     * Set the value of username
     *
     * @return  self
     */ 
    public function setUsername($username)
    {
            $this->username = $username;
            return $this;
    }
}