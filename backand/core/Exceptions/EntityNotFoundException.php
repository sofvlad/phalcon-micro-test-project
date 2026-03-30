<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Throwable;

class EntityNotFoundException extends Exception
{
    public function __construct(string $class, int $code = 404, ?Throwable $previous = null)
    {
        $separatedClassNamespace = explode('\\', $class);
        parent::__construct(sprintf('%s not found', array_pop($separatedClassNamespace)), $code, $previous);
    }
}
