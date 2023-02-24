<?php
namespace Person;
class Name
{
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {
    }

    public function __toString()
    {
        return 'Имя: ' . $this->firstName . ' Фамилия: ' . $this->lastName;
    }

    public function first(): string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function last(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

}