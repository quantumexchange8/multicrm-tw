<?php

namespace App\Exceptions\Auth;

use App\Exceptions\CustomException;

class InvalidCredentialException extends CustomException
{
    public function __construct($messages = null)
    {
        if (!$messages) {
            $messages = "credentials not match";
        }
        parent::__construct($messages, 101);
    }
}
