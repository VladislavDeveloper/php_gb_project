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

        /**
         * Get the value of firstName
         */ 
        public function getFirstName()
        {
                return $this->firstName;
        }

        /**
         * Set the value of firstName
         *
         * @return  self
         */ 
        public function setFirstName($firstName)
        {
                $this->firstName = $firstName;

                return $this;
        }

        /**
         * Get the value of lastName
         */ 
        public function getLastName()
        {
                return $this->lastName;
        }

        /**
         * Set the value of lastName
         *
         * @return  self
         */ 
        public function setLastName($lastName)
        {
                $this->lastName = $lastName;

                return $this;
        }
}