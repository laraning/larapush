<?php

namespace Laraning\Larapush\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

final class LocalException extends Exception
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
        parent::__construct($this->message);
    }

    public function report()
    {
        Log::error($this->message);
    }
}
