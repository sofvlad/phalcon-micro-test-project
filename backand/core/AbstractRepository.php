<?php

declare(strict_types=1);

namespace Core;

use Core\Builder\Order;
use Core\Exceptions\EntityNotFoundException;
use Core\Exceptions\Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

abstract class AbstractRepository
{
    public function __construct(protected string $modelClass, protected ModelManager $manager)
    {
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return new $this->modelClass();
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    protected function getModelProperties(): array
    {
        $properties = new ReflectionClass($this->modelClass)->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($properties as $property) {
            $result[] = $property->name;
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @return array
     * @throws ReflectionException
     */
    protected function filterParameters(array $parameters): array
    {
        return array_intersect_key($parameters, array_flip($this->getModelProperties()));
    }

    /**
     * @param array $parameters
     * @return BuilderInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function getBuilder(array $parameters = []): BuilderInterface
    {
        $qb = $this->manager->createBuilder()->addFrom($this->modelClass, 't1');

        if (!empty($parameters['order'])) {
            $order = $parameters['order'];

            if (!in_array($order['field'], $this->getModelProperties())) {
                throw new Exception('Order field does not exist');
            }

            $dir = strtolower($order['dir']);
            if (Order::tryFrom($dir) === null) {
                throw new Exception('Order dir must be ASC or DESC');
            }
            $qb->orderBy(sprintf(
                't1.%s %s',
                $order['field'],
                $dir
            ));
        }

        if (empty($parameters)) {
            return $qb;
        }

        $parameters = $this->filterParameters($parameters);
        foreach ($parameters as $param => $value) {
            $qb->andWhere(sprintf('%s = :%s:', $param, $param), [$param => $value]);
        }

        return $qb;
    }

    /**
     * @param array $parameters
     * @return Resultset
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function find(array $parameters): Resultset
    {
        $model = $this->getModel();
        $result = $model::find($this->filterParameters($parameters));
        if (empty($result)) {
            throw new EntityNotFoundException($model::class);
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @return Resultset
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function findFirst(array $parameters): Resultset
    {
        $model = $this->getModel();
        $result = $model::findFirst($this->filterParameters($parameters));
        if (empty($result)) {
            throw new EntityNotFoundException($model::class);
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @return bool
     * @throws EntityNotFoundException
     * @throws ReflectionException
     */
    public function delete(array $parameters): bool
    {
        return $this->findFirst($this->filterParameters($parameters))->delete();
    }
}
