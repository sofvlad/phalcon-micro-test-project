<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Config\Config;

final readonly class ConfigFactory implements ServiceFactory
{
    public static function create(): object
    {
        $configArr = [];

        foreach (glob(ROOT_PATH . '/config/*.php') as $file) {
            $data = include $file;
            [$fileName,] = explode('.', basename($file));

            if (is_array($data)) {
                $configArr[$fileName] = $data;
            }
        }

        return new Config($configArr);
    }
}
