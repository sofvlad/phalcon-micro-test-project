<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Core\Exceptions\Exception;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\QueryBuilder;

class ProductController extends Controller
{
    /**
     * @throws Exception
     */
    public function list(): ResponseInterface
    {
        $productRepository = new ProductRepository($this->getDI());
        $jsonData = $this->request->getJsonRawBody(true);
        $builder = $productRepository->getBuilder($jsonData);

        $paginator = new QueryBuilder(
            [
                'builder' => $builder,
                'limit'   => min($jsonData['limit'] ?: 10, 100),
                'page'    => $jsonData['page'],
            ]
        );

        $pageData = $paginator->paginate();

        return $this->response->setJsonContent([
            'items' => $pageData->getItems(),
            'page' => $pageData->getCurrent(),
            'total' => $pageData->getTotalItems(),
        ]);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     */
    public function view(int $id): ResponseInterface
    {
        $product = Product::findFirst($id);
        $result = $product->toArray();
        $result['categories'] = array_column($product->getRelated(Category::class)->toArray(), 'id');

        return $this->response->setJsonContent($result);
    }

    /**
     * @return ResponseInterface
     */
    public function save(): ResponseInterface
    {
        $product = new ProductRepository($this->getDI())->save($this->request->getJsonRawBody(true));
        $result = $product->toArray();
        $result['categories'] = array_column($product->getRelated(Category::class)->toArray(), 'id');

        return $this->response->setJsonContent($result);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        Product::findFirst($id)->delete();

        return $this->response->setJsonContent([]);
    }
}
