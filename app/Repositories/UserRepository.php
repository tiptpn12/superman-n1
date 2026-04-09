<?php

namespace App\Repositories;

use App\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getById($id)
    {
        return User::findOrFail($id);
    }
}
