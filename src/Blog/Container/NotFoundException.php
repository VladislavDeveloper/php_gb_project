<?php

namespace Blog\Container;
use Blog\Exceptions\AppException;
use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{
    
}