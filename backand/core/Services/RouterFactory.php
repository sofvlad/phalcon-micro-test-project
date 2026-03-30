<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Mvc\Router;

final readonly class RouterFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new Router();
    }
}
