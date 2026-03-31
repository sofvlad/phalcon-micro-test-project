<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Core\AbstractController;
use Core\Exceptions\EntityNotFoundException;
use Core\Exceptions\Exception;
use Core\Exceptions\UnauthorizedException;
use Phalcon\Encryption\Security\JWT\Exceptions\UnsupportedAlgorithmException;
use Phalcon\Http\ResponseInterface;

class CategoryController extends AbstractController
{
    /**
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
    public function view(int $id): ResponseInterface
    {
        return $this->response->setJsonContent(Category::findFirst($id));
    }

    /**
     * @return ResponseInterface
     * @throws EntityNotFoundException
     * @throws UnauthorizedException
     * @throws UnsupportedAlgorithmException
     */
    public function save(): ResponseInterface
    {
        $this->getCurrentUser();
        return $this->response->setJsonContent(
            new CategoryRepository($this->getDI())->save($this->request->getJsonRawBody(true))
        );
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @throws EntityNotFoundException
     * @throws UnauthorizedException
     * @throws UnsupportedAlgorithmException
     */
    public function delete(int $id): ResponseInterface
    {
        $this->getCurrentUser();
        Category::findFirst($id)->delete();

        return $this->response->setJsonContent([]);
    }
}
