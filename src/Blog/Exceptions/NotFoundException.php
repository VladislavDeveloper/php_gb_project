<?php

namespace Blog\Exceptions;

use Blog\Exceptions\AppException;
use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AppException implements NotFoundExceptionInterface
{
    
}