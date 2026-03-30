<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Core\AbstractRepository;
use Core\Exceptions\EntityNotFoundException;
use Core\Exceptions\Exception;
use Phalcon\Config\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Encryption\Security;
use Phalcon\Encryption\Security\JWT\Builder as JWTBuilder;
use Phalcon\Encryption\Security\JWT\Exceptions\UnsupportedAlgorithmException;
use Phalcon\Encryption\Security\JWT\Exceptions\ValidatorException;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Http\Request;

class UserRepository extends AbstractRepository
{
    public function __construct(protected DiInterface $di)
    {
        parent::__construct(User::class, $di->get('modelsManager'));
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return array
     *
     * @throws EntityNotFoundException
     * @throws Exception
     * @throws UnsupportedAlgorithmException
     * @throws ValidatorException
     */
    public function auth(string $email, string $password): array
    {
        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => [
                'email' => $email,
            ],
        ]);
        if ($user === null) {
            throw new EntityNotFoundException(User::class);
        }

        if (empty($password)) {
            throw new Exception('Password is not set');
        }
        $checkPassword = new Security()->checkHash($password, $user->password);
        if (!$checkPassword) {
            throw new EntityNotFoundException(User::class);
        }

        /** @var Config $config */
        $config = $this->di->get('config');
        /** @var Request $request */
        $request = $this->di->get('request');

        $builder = new JWTBuilder(new Hmac($config->path('jwt.algorithm')));
        $now = time();
        $expire = $now + $config['jwt']['expires'];

        $tokenObject = $builder
            ->setId(uniqid())
            ->setSubject($email)
            ->setAudience($request->getHeader('referer') ?? $config->path('jwt.audience'))
            ->setIssuer($config->path('jwt.issuer'))
            ->setIssuedAt($now)
            ->setNotBefore($now)
            ->setExpirationTime($expire)
            ->setPassphrase($config->path('jwt.secret'))
            ->getToken();

        return [
            'token' => $tokenObject->getToken(),
            'expires_in' => $expire,
        ];
    }
}
