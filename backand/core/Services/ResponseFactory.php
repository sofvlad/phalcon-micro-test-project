<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Http\Response;

final readonly class ResponseFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new Response();
    }
}
