<?php
namespace Blog\Post;
use Blog\User\User;
use Blog\UUID\UUID;
class Post
{

    public function __construct(
        private UUID $uuid, 
        private User $user, 
        private string $title, 
        private string $text
    )
    {
    }


        /**
         * Get the value of uuid
         */ 
        public function getUuid(): UUID
        {
                return $this->uuid;
        }

        /**
         * Set the value of uuid
         *
         * @return  self
         */ 
        public function setUuid(UUID $uuid)
        {
                $this->uuid = $uuid;

                return $this;
        }

        /**
         * Get the value of user
         */ 
        public function getUser(): User
        {
                return $this->user;
        }

        /**
         * Set the value of user
         *
         * @return  self
         */ 
        public function setUser(User $user)
        {
                $this->user = $user;

                return $this;
        }

        /**
         * Get the value of title
         */ 
        public function getTitle(): string
        {
                return $this->title;
        }

        /**
         * Set the value of title
         *
         * @return  self
         */ 
        public function setTitle(string $title)
        {
                $this->title = $title;

                return $this;
        }

        /**
         * Get the value of text
         */ 
        public function getText(): string
        {
                return $this->text;
        }

        /**
         * Set the value of text
         *
         * @return  self
         */ 
        public function setText(string $text)
        {
                $this->text = $text;

                return $this;
        }
}