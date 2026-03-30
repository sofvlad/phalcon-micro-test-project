<?php

declare(strict_types=1);

namespace Core\Middleware;

enum Event: string
{
    case AFTER = 'after';
    case BEFORE = 'before';
    case FINISH = 'finish';
}
