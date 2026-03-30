<?php

declare(strict_types=1);

namespace Core\Services;

use Dotenv\Dotenv;

final readonly class ENVFactory implements ServiceFactory
{
    public static function create(): object
    {
        $dotenv = Dotenv::createImmutable(ROOT_PATH);
        $dotenv->load();

        return $dotenv;
    }
}
