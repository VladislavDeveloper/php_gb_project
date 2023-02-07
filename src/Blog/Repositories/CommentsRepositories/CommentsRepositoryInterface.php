<?php

namespace Blog\Repositories\CommentsRepositories;
use Blog\Comment\Comment;
use Blog\UUID\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}