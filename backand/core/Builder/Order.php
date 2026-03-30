<?php

declare(strict_types=1);

namespace Core\Builder;

enum Order: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
