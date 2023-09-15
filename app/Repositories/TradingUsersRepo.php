<?php

namespace App\Repositories;

use App\Models\TradingUser;

class TradingUsersRepo extends AppRepo
{
    public function __construct()
    {
        parent::__construct(new TradingUser);
    }
}