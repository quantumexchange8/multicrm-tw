<?php

namespace App\Repositories;

use App\Models\TradingAccount;

class TradingAccountsRepo extends AppRepo
{
    public function __construct()
    {
        parent::__construct(new TradingAccount);
    }
}