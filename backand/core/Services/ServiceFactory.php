<?php

declare(strict_types=1);

namespace Core\Services;

interface ServiceFactory
{
    public static function create(): object;
}
