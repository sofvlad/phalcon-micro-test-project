<?php

declare(strict_types=1);

namespace Core\Exceptions;

class Exception extends \Exception
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }
}
