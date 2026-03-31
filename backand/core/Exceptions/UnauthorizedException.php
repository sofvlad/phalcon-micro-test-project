<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Throwable;

class UnauthorizedException extends Exception
{
    public function __construct(string $message, int $code = 401, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
