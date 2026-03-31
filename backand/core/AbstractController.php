<?php

declare(strict_types=1);

namespace Core;

use App\Models\User;
use Core\Exceptions\EntityNotFoundException;
use Core\Exceptions\UnauthorizedException;
use Phalcon\Config\Config;
use Phalcon\Encryption\Security\JWT\Exceptions\UnsupportedAlgorithmException;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Token\Enum as JWTEnum;
use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Mvc\Controller;

abstract class AbstractController extends Controller
{
    /**
     * @throws UnsupportedAlgorithmException
     * @throws UnauthorizedException
     * @throws EntityNotFoundException
     */
    protected function getCurrentUser(): ?array
    {
        $authHeader = $this->request->getHeader('Authorization');
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new UnauthorizedException('Please provide a valid authentication token');
        }

        /** @var Config $config */
        $config = $this->getDI()->get('config');

        $parser = new Parser();
        $signer = new Hmac($config->path('jwt.algorithm'));

        $token = $parser->parse($matches[1]);
        if (!$token->verify($signer, $config->path('jwt.secret'))) {
            throw new UnauthorizedException('Authentication token is not valid');
        }
        $claims = $token->getClaims();

        $expirationTime = $claims->get(JWTEnum::EXPIRATION_TIME);
        if ($expirationTime < time()) {
            throw new UnauthorizedException('Authentication token is expired');
        }

        $subject = $claims->get(JWTEnum::SUBJECT);
        if (empty($subject)) {
            throw new UnauthorizedException('Authentication token is not valid');
        }
        [, $id] = explode('_', $claims->get(JWTEnum::SUBJECT));
        $user = User::findFirst($id);
        if (empty($user)) {
            throw new EntityNotFoundException(User::class);
        }

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ];
    }
}
