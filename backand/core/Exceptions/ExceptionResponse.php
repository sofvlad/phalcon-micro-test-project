<?php

declare(strict_types=1);

namespace Core\Exceptions;

use JetBrains\PhpStorm\NoReturn;
use Phalcon\Http\Response;
use Throwable;

final class ExceptionResponse
{
    #[NoReturn]
    public static function handle(Throwable $exception): void
    {
        $response = new Response();
        $response->setContentType('application/json', 'UTF-8');
        $response->setStatusCode(500);
        $responseContent = [
            'status' => 'error',
            'code' => $exception->getCode(),
            'message' => ($_ENV['DEBUG'] || $exception instanceof Exception)
                ? $exception->getMessage()
                : 'Internal server error',
        ];
        if ($exception instanceof Exception) {
            $responseContent['data'] = $exception->getData();
        }
        if ($_ENV['DEBUG']) {
            $responseContent['file'] = $exception->getFile();
            $responseContent['line'] = $exception->getLine();
            $responseContent['trace'] = $exception->getTrace();
        }
        $response->setJsonContent($responseContent)->send();
        die;
    }
}
