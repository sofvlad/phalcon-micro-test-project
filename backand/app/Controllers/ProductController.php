<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Core\Builder\Order;
use Core\Exceptions\Exception;
use Core\Exceptions\ValidateException;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\QueryBuilder;
use ReflectionException;

class ProductController extends Controller
{
    /**
     * @throws ReflectionException
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
        return $this->response->setJsonContent(Product::findFirst($id));
    }

    /**
     * @return ResponseInterface
     */
    public function save(): ResponseInterface
    {
        $request = $this->request;

        $id = (int)$request->getPost('id');
        if (!empty($id)) {
            $product = Product::findFirst($id);
        }
        $product ??= (new Product());

        $product->setId($id);
        $product->setName($request->getPost('name', null, $product->getName()));
        $product->setDescription($request->getPost('description', null, $product->getDescription()));
        $product->setPrice((float)$request->getPost('price', null, $product->getPrice()));
        $product->setInStock((bool)$request->getPost('in_stock', null, $product->getInStock()));
        $product->save();

        return $this->response->setJsonContent($product);
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
