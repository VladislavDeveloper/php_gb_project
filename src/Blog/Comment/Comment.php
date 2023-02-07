<?php
namespace Blog\Comment;
use Blog\Post\Post;
use Blog\User\User;
use Blog\UUID\UUID;
class Comment
{
    public function __construct(
        private UUID $uuid,
        private Post $post,
        private User $author,
        private string $text,
    )
    {
    }

    public function __toString(): string
    {
        return "Комментарий: $this->text";
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
         * Get the value of post_uuid
         */ 
        public function getPost(): Post
        {
                return $this->post;
        }

        /**
         * Set the value of post_uuid
         *
         * @return  self
         */ 
        public function setPost(Post $post)
        {
                $this->post = $post;

                return $this;
        }

        /**
         * Get the value of author_uuid
         */ 
        public function getAuthor(): User
        {
                return $this->author;
        }

        /**
         * Set the value of author_uuid
         *
         * @return  self
         */ 
        public function setAuthor_uuid(User $author)
        {
                $this->author = $author;

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