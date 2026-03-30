<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Mvc\Model\Manager as ModelManager;

final readonly class ModelsManagerFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new ModelManager();
    }
}
