<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use Core\AbstractRepository;
use Core\Exceptions\Exception;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use ReflectionException;

class ProductRepository extends AbstractRepository
{
    public function __construct(protected DiInterface $di)
    {
        parent::__construct(Product::class, $di->get('modelsManager'));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function getBuilder(array $parameters = []): BuilderInterface
    {
        if (isset($parameters['in_stock'])) {
            $parameters['in_stock'] = !empty($parameters['in_stock']);
        }
        $qb = parent::getBuilder($parameters);

        if (empty($parameters['category'])) {
            return $qb;
        }
        $qb->join(CategoryProduct::class, '[t1].id = [t2].product_id', 't2');

        if (is_int($parameters['category'])) {
            $qb->where('[t2].category_id = :category_id:', ['category_id' => $parameters['category']]);

            return $qb;
        }
        $qb->join(Category::class, '[t2].category_id = [t3].id', 't3');
        $qb->andWhere('t3.code = :category_code:', ['category_code' => $parameters['category']]);

        return $qb;
    }
}
