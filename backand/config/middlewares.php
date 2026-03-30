<?php

use App\Middlewares\ResponseMiddleware;

return [
    'before' => [],
    'after' => [
        ResponseMiddleware::class,
    ],
    'finish' => [],
];
