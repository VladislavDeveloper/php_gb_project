<?php

namespace Blog\Http\Auth;
use Blog\Http\Request;
use Blog\User\User;

interface IdentificationInterface
{
    public function user(Request $request): User;
}