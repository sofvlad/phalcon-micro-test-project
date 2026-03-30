<?php

declare(strict_types=1);

namespace App\Middlewares;

use Phalcon\Http\Response;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;

/**
 * ResponseMiddleware
 *
 * @property Response $response
 */
class ResponseMiddleware implements MiddlewareInterface
{
    /**
     * @param Micro $application
     * @return void
     */
    public function call(Micro $application): void
    {
        /** @var ResponseInterface $response */
        $response = $application->getReturnedValue();
        $content = $response->getContent();

        $payload = [
            'status'  => 'success',
        ];

        if ($response->getStatusCode() >= 300) {
            $payload['status'] = 'error';
        }

        if (json_validate($content)) {
            if ($response->getStatusCode() >= 300) {
                $payload = array_merge($payload, json_decode($content, true));
            } else {
                $payload['data'] = json_decode($content, true);
            }
        } else {
            $payload['message'] = $content;
        }

        $application
            ->response
            ->setJsonContent($payload);
    }
}