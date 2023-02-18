<?php

namespace Blog\Http\Auth;
use Blog\Http\Request;
use Blog\User\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}