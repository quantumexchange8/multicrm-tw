<?php

namespace App\Exceptions;

class CustomException extends \Exception
{
    protected $custom_code;

    public function __construct($message, $customCode = 100)
    {
        parent::__construct($message, 422);

        $this->custom_code = $customCode;
    }

    public function getCustomCode()
    {
        return $this->custom_code;
    }
}
