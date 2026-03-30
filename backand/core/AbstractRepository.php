<?php

declare(strict_types=1);

namespace Core;

use Core\Builder\Order;
use Core\Exceptions\Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\Query\BuilderInterface;

abstract class AbstractRepository
{
    public function __construct(protected string $modelClass, protected ModelManager $manager)
    {
    }

    /**
     * @return AbstractModel
     */
    public function getModel(): AbstractModel
    {
        return new $this->modelClass();
    }

    /**
     * @param Model|null $model
     * @return array
     */
    protected function getModelProperties(?Model $model = null): array
    {
        if ($model === null) {
            $model = $this->getModel();
        }

        return $model->getModelsMetaData()->getAttributes($model);
    }

    /**
     * @param array $parameters
     * @param Model|null $model
     * @return array
     */
    protected function filterParameters(array $parameters, ?Model $model = null): array
    {
        return array_intersect_key($parameters, array_flip($this->getModelProperties($model)));
    }

    /**
     * @param array $parameters
     * @return BuilderInterface
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
     * @param array $data
     * @return AbstractModel
     */
    public function save(array $data): AbstractModel
    {
        $model = $this->getModel();
        $data = $this->filterParameters($data, $model);
        $primaryKeyVal = $data[$model->getPrimaryKey()];
        if (!empty($primaryKeyVal)) {
            $model = $model::findFirst($primaryKeyVal);
        }
        $model->assign($data)->save();

        return $model;
    }
}
