<?php

declare(strict_types=1);

namespace Core\Services;

use Phalcon\Mvc\Model\MetaData\Memory as ModelMemoryMetaData;

final readonly class ModelsMetadataFactory implements ServiceFactory
{
    public static function create(): object
    {
        return new ModelMemoryMetaData();
    }
}
