<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use Core\AbstractModel;
use Core\AbstractRepository;
use Core\Exceptions\Exception;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Model\Query\BuilderInterface;

class ProductRepository extends AbstractRepository
{
    public function __construct(protected DiInterface $di)
    {
        parent::__construct(Product::class, $di->get('modelsManager'));
    }

    /**
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

    /**
     * @param array $data
     * @return AbstractModel
     */
    public function save(array $data): AbstractModel
    {
        /** @var Product $product */
        $product = parent::save($data);
        if (!isset($data['categories'])) {
            return $product;
        }
        $categories = CategoryProduct::find([
            'conditions' => 'product_id = :product_id:',
            'bind'       => [
                'product_id' => $product->getId(),
            ],
        ]);
        $existIds = array_column($categories->toArray(), 'category_id');
        $getIds = array_map('intval', array_filter($data['categories'], 'is_numeric'));
        $getIds = array_values(array_unique($getIds));
        $toAdd = array_diff($getIds, $existIds);
        $toRemove = array_diff($existIds, $getIds);

        foreach ($toRemove as $categoryId) {
            CategoryProduct::findFirst([
                'conditions' => 'product_id = :product_id: AND category_id = :category_id:',
                'bind'       => [
                    'product_id' => $product->getId(),
                    'category_id' => $categoryId,
                ],
            ])->delete();
        }
        foreach ($toAdd as $categoryId) {
            $categoryProduct = new CategoryProduct();
            $categoryProduct->setCategoryId($categoryId);
            $categoryProduct->setProductId($product->getId());
            $categoryProduct->save();
        }

        return $product;
    }
}
