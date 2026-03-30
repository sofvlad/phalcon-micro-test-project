<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Db\Adapter\Pdo\Mysql;

final readonly class DBFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new Mysql([
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'dbname' => $_ENV['DB_NAME'],
            'charset' => 'utf8'
        ]);
    }
}
