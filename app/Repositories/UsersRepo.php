<?php

namespace App\Repositories;

use App\Models\User;

class UsersRepo extends AppRepo
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function updateMetaLogin(User $user, int $metaLogin): User
    {
        return $this->update($user->id, [
            "meta_login" => $metaLogin
        ]);
    }

    public function updateLastLoginIp(User $user, string $ip): User
    {
        return $this->update($user->id, [
            "last_login_ip" => $ip
        ]);
    }
}