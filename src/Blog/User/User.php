<?php

namespace Blog\User;

use Blog\UUID\UUID;
use Person\Name;
class User
{
    public function __construct(
        private UUID $uuid,
        private Name $name,
        private string $username
    ){
    }

    public function __toString(): string
    {
        return "Пользователь $this->username $this->name";
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