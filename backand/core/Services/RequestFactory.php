<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Http\Request;

final readonly class RequestFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new Request();
    }
}
