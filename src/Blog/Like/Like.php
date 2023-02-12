<?php

namespace Blog\Like;
use Blog\UUID\UUID;

class Like
{
    public function __construct(
        private UUID $uuid,
        private UUID $postUuid,
        private UUID $authorUuid,
    ){
    }

        /**
         * Get the value of uuid
         */ 
        public function getUuid()
        {
                return $this->uuid;
        }

        /**
         * Set the value of uuid
         *
         * @return  self
         */ 
        public function setUuid($uuid)
        {
                $this->uuid = $uuid;

                return $this;
        }

        /**
         * Get the value of postUuid
         */ 
        public function getPostUuid()
        {
                return $this->postUuid;
        }

        /**
         * Set the value of postUuid
         *
         * @return  self
         */ 
        public function setPostUuid($postUuid)
        {
                $this->postUuid = $postUuid;

                return $this;
        }

        /**
         * Get the value of authorUuid
         */ 
        public function getAuthorUuid()
        {
                return $this->authorUuid;
        }

        /**
         * Set the value of authorUuid
         *
         * @return  self
         */ 
        public function setAuthorUuid($authorUuid)
        {
                $this->authorUuid = $authorUuid;

                return $this;
        }
}