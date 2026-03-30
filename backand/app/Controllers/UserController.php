<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Core\Exceptions\EntityNotFoundException;
use Core\Exceptions\Exception;
use Phalcon\Encryption\Security\JWT\Exceptions\UnsupportedAlgorithmException;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    /**
     * @throws EntityNotFoundException
     * @throws Exception
     * @throws ValidatorException
     * @throws UnsupportedAlgorithmException
     */
    public function login(): ResponseInterface
    {
        $jsonData = $this->request->getJsonRawBody(true);

        return $this->response->setJsonContent(
            new UserRepository($this->getDI())
                ->auth($jsonData['email'], $jsonData['password'])
        );
    }

    public function register(): ResponseInterface
    {
        $jsonData = $this->request->getJsonRawBody(true);

        $user = new User([
            'email' => $jsonData['email'],
            'password' => $jsonData['password'],
        ]);
        $user->save();

        return $this->response->setJsonContent([]);
    }
}
