<?php

namespace App\Exceptions;

use RuntimeException;

class IdempotencyKeyAlreadyProcessingException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'This request is already being processed. Please wait.'
        );
    }
}