<?php

declare(strict_types=1);

namespace Core\Exceptions;

use Phalcon\Messages\Messages;
use Throwable;

class ValidateException extends Exception
{
    public function __construct(protected Messages $messages, int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct('Ошибка валидации', $code, $previous);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return array_map(static function ($message) {
            return [
                'field' => $message['field'],
                'message' => $message['message'],
            ];
        }, $this->messages->jsonSerialize());
    }
}
