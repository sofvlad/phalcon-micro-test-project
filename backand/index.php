<?php

use Core\Exceptions\ExceptionResponse;
use Core\Middleware\MiddlewareRegister;
use Core\Router\RouterRegister;
use Core\Services\ConfigFactory;
use Core\Services\DBFactory;
use Core\Services\ENVFactory;
use Core\Services\ModelsManagerFactory;
use Core\Services\ModelsMetadataFactory;
use Core\Services\RequestFactory;
use Core\Services\ResponseFactory;
use Core\Services\RouterFactory;
use Phalcon\Di\Di;
use Phalcon\Mvc\Micro;

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

const ROOT_PATH = __DIR__;

require_once(ROOT_PATH . '/vendor/autoload.php');

try {
    $app = new Micro((new Di()));

    $app->setService('env', ENVFactory::create(), true);
    $app->setService('config', ConfigFactory::create(), true);
    $app->setService('db', DBFactory::create(), true);
    $app->setService('request', RequestFactory::create(), true);
    $app->setService('response', ResponseFactory::create(), true);
    $app->setService('router', RouterFactory::create(), true);
    $app->setService('modelsManager', ModelsManagerFactory::create(), true);
    $app->setService('modelsMetadata', ModelsMetadataFactory::create(), true);

    new RouterRegister($app)->register();
    new MiddlewareRegister($app)->register();

    $app->handle($_SERVER["REQUEST_URI"]);
} catch (Throwable $e) {
    ExceptionResponse::handle($e);
}
