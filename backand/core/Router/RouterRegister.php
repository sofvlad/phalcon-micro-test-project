<?php

namespace Core\Router;

use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection;

/**
 * Регистратор маршрутов для микро-приложения Phalcon.
 * Преобразует конфигурационный массив в коллекции и регистрирует их.
 */
class RouterRegister
{
    private array $collections = [];

    /**
     * @param Micro $app
     */
    public function __construct(private readonly Micro $app)
    {
    }

    /**
     * Регистрирует маршруты из конфигурационного массива
     *
     * @return void
     */
    public function register(): void
    {
        $this->collectRoutes($this->app->getDI()->get('config')->get('router')->toArray());
        foreach ($this->collections as $collection) {
            $this->app->mount($collection);
        }

        /** @var Response $response */
        $response = $this->app->getDI()->get('response');
        $this->app->notFound(static function () use ($response) {
            return $response
                ->setStatusCode(404)
                ->setJsonContent([
                    'status' => 'error',
                    'message' => 'Not Found'
                ]);
        });
    }

    /**
     * Собирает маршруты и генерирует коллекции
     *
     * @param array $config
     * @param string $prefix
     */
    private function collectRoutes(array $config, string $prefix = ''): void
    {
        foreach ($config as $item) {
            if ($item['routes']) {
                $this->collectRoutes($item['routes'], $prefix . $item['prefix']);
                continue;
            }

            [$controllerClass, $controllerAction] = $item['handler'];
            $item['action'] = $controllerAction;
            unset($item['handler']);

            if (!isset($this->collections[$controllerClass])) {
                $this->collections[$controllerClass] = $this->createCollection($controllerClass, $prefix);
            }

            $this->addRouteToCollection($this->collections[$controllerClass], $item);
        }
    }

    /**
     * Создаёт и настраивает коллекцию для группы маршрутов.
     *
     * @param string $handler
     * @param string $prefix
     * @return Collection
     */
    private function createCollection(string $handler, string $prefix): Collection
    {
        $collection = new Collection();
        if (!empty($prefix)) {
            $prefix = '/' . trim($prefix, '/');
            $collection->setPrefix($prefix);
        }

        return $collection
            ->setHandler($handler)
            ->setLazy(true);
    }

    /**
     * Добавляет отдельный маршрут в коллекцию.
     *
     * @param Collection $collection
     * @param array $route
     * @return void
     */
    private function addRouteToCollection(Collection $collection, array $route): void
    {
        $pattern = '/' . trim($route['pattern'], '/');
        $method = $route['method'];
        $action = $route['action'];
        $name = $route['name'] ?? null;

        $httpMethod = $method instanceof Method ? $method->value : strtoupper($method);

        switch ($httpMethod) {
            case Method::GET->value:
                $collection->get($pattern, $action, $name);
                break;
            case Method::POST->value:
                $collection->post($pattern, $action, $name);
                break;
            case Method::PUT->value:
                $collection->put($pattern, $action, $name);
                break;
            case Method::PATCH->value:
                $collection->patch($pattern, $action, $name);
                break;
            case Method::DELETE->value:
                $collection->delete($pattern, $action, $name);
                break;
            case Method::HEAD->value:
                $collection->head($pattern, $action, $name);
                break;
            case Method::OPTIONS->value:
                $collection->options($pattern, $action, $name);
                break;
            default:
                $collection->mapVia($pattern, $action, [$httpMethod], $name);
                break;
        }
    }
}
