<?php
namespace User;
class User
{
    private int $id;
    private string $name;
    private string $lastName;

    public function __construct(int $id, string $name, string $lastName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
    }

    public function __toString(): string
    {
        return "Пользователь $this->name $this->lastName";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }
 
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
}