<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Core\Exceptions\Exception;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;

class CategoryController extends Controller
{
    /**
     * @throws \ReflectionException
     * @throws Exception
     */
    public function list(): ResponseInterface
    {
        return $this->response->setJsonContent(
            new CategoryRepository($this->getDI())
                ->getBuilder($this->request->getJsonRawBody(true))
                ->getQuery()
                ->execute()
        );
    }

    /**
     * @param int $id
     * @return ResponseInterface
     */
    public function delete(int $id): ResponseInterface
    {
        Category::findFirst($id)->delete();

        return $this->response->setJsonContent([]);
    }
}
