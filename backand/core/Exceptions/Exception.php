<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Throwable;

class Exception extends \Exception
{
    public function __construct(string $message = "", int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }
}
