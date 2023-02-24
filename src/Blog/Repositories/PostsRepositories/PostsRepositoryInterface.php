<?php

namespace Blog\Repositories\PostsRepositories;
use Blog\UUID\UUID;
use Blog\Post\Post;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}