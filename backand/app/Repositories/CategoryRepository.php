<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryProduct;
use Core\AbstractModel;
use Core\AbstractRepository;
use Phalcon\Di\DiInterface;

class CategoryRepository extends AbstractRepository
{
    public function __construct(protected DiInterface $di)
    {
        parent::__construct(Category::class, $di->get('modelsManager'));
    }

    /**
     * @param array $data
     * @return AbstractModel
     */
    public function save(array $data): AbstractModel
    {
        /** @var Category $category */
        $category = parent::save($data);
        if (empty($data['products'])) {
            return $category;
        }
        foreach (array_unique(array_filter($data['products'])) as $productId) {
            $categoryProduct = new CategoryProduct();
            $categoryProduct->setCategoryId($category->getId());
            $categoryProduct->setProductId($productId);
            $categoryProduct->save();
        }

        return $category;
    }
}
