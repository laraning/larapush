<?php

namespace Laraning\Larapush\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

final class AccessTokenException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
