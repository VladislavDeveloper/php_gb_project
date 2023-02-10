<?php

namespace Blog\Repositories\LikesRepository;

use Blog\Like\Like;
use Blog\UUID\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $post_uuid): array;
}