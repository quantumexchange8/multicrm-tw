<?php

namespace App\Services\MetaTrader5;

use AleeDhillon\MetaFive\Facades\Client;

class BaseApi
{
    protected $api;

    public function __construct()
    {
        $this->api = new Client();
    }
}
