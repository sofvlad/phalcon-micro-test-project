<?php

use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use Core\Router\Method;

return [
    [
        'prefix' => '/api/v1',
        'routes' => [
            [
                'pattern' => '/user',
                'method'  => Method::POST,
                'handler'  => [UserController::class, 'login'],
                'name' => 'user.login',
            ],
            [
                'pattern' => '/user/register',
                'method'  => Method::POST,
                'handler'  => [UserController::class, 'register'],
                'name' => 'user.register',
            ],
            [
                'prefix' => '/product',
                'routes' => [
                    [
                        'pattern' => '/list',
                        'method'  => Method::POST,
                        'handler'  => [ProductController::class, 'list'],
                        'name' => 'product.list',
                    ],
                    [
                        'pattern' => '/{id}',
                        'method'  => Method::GET,
                        'handler'  => [ProductController::class, 'view'],
                        'name' => 'product.view',
                    ],
                    [
                        'pattern' => '/',
                        'method'  => Method::POST,
                        'handler'  => [ProductController::class, 'save'],
                        'name' => 'product.save',
                    ],
                    [
                        'pattern' => '/{id}',
                        'method'  => Method::DELETE,
                        'handler'  => [ProductController::class, 'delete'],
                        'name' => 'product.delete',
                    ],
                ],
            ],
            [
                'prefix' => '/category',
                'routes' => [
                    [
                        'pattern' => '/list',
                        'method'  => Method::POST,
                        'handler'  => [CategoryController::class, 'list'],
                        'name' => 'category.list',
                    ],
                    [
                        'pattern' => '/{id}',
                        'method'  => Method::GET,
                        'handler'  => [CategoryController::class, 'view'],
                        'name' => 'category.view',
                    ],
                    [
                        'pattern' => '/',
                        'method'  => Method::POST,
                        'handler'  => [CategoryController::class, 'save'],
                        'name' => 'category.save',
                    ],
                    [
                        'pattern' => '/{id}',
                        'method'  => Method::DELETE,
                        'handler'  => [CategoryController::class, 'delete'],
                        'name' => 'category.delete',
                    ],
                ],
            ],
        ],
    ],
];
