<?php

namespace Core\Middleware;

use Phalcon\Events\Manager;
use Phalcon\Mvc\Micro;

/**
 * Регистратор промежуточных обработчиков для микро-приложения Phalcon.
 */
readonly class MiddlewareRegister
{
    /**
     * @param Micro $app
     */
    public function __construct(private Micro $app)
    {
    }

    /**
     * Регистрирует события
     *
     * @return void
     */
    public function register(): void
    {
        $eventsManager = new Manager();
        $eventMiddlewares = $this->app->getDI()->get('config')->get('middlewares')->toArray();
        foreach ($eventMiddlewares as $event => $handlers) {
            foreach ($handlers as $handler) {
                if (empty(Event::tryFrom($event))) {
                    $eventsManager->attach($event, new $handler());
                } else {
                    $this->app->$event(new $handler());
                }
            }
        }

        $this->app->setEventsManager($eventsManager);
    }
}
