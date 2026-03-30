<?php

declare(strict_types=1);

namespace App\Models;

use Core\AbstractModel;
use Core\Exceptions\ValidateException;
use Phalcon\Encryption\Security;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\Regex;
use Phalcon\Filter\Validation\Validator\Uniqueness;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Mvc\Model\MetaDataInterface;

/**
 * @method int getId()
 * @method void setId(int $value)
 * @method string getEmail()
 * @method void setEmail(string $value)
 * @method string getPassword()
 * @method string setPassword(string $value)
 */
class User extends AbstractModel
{
    public ?int $id = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function initialize(): void
    {
        $this->setSource('users');
        $this->setPrimaryKey('id');

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field'  => 'created_at',
                    'format' => 'Y-m-d H:i:s'
                ],
                'beforeUpdate' => [
                    'field'  => 'updated_at',
                    'format' => 'Y-m-d H:i:s'
                ]
            ])
        );
    }

    /**
     * @return void
     * @throws ValidateException
     */
    public function validation(): void
    {
        $validator = new Validation()
            ->add(
                'email',
                new PresenceOf([
                    'message' => 'Email обязателен',
                    'cancelOnFail' => true,
                ])
            )
            ->add(
                'email',
                new Email([
                    'message' => 'Некорректный формат email',
                ])
            )
            ->add(
                'email',
                new StringLength([
                    'min' => 6,
                    'max' => 254,
                    'messageMinimum' => 'Email должен содержать минимум 6 символов',
                    'messageMaximum' => 'Email не должен превышать 254 символов',
                ])
            )
            ->add(
                'password',
                new PresenceOf([
                    'message' => 'Пароль обязателен',
                    'cancelOnFail' => true,
                ])
            )
            ->add(
                'password',
                new StringLength([
                    'min' => 8,
                    'max' => 128,
                    'messageMinimum' => 'Пароль должен содержать минимум 8 символов',
                    'messageMaximum' => 'Пароль не должен превышать 128 символов',
                ])
            )
            ->add(
                'password',
                new Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/',
                    'message' => 'Пароль должен содержать заглавные/строчные буквы, цифры и быть ≥8 символов',
                ])
            );

        if (empty($this->getId())) {
            $validator->add(
                'email',
                new Uniqueness(
                    [
                        "model" => new User(),
                        "attribute" => "email",
                        'message' => 'Пользователь с таким email уже есть',
                    ]
                )
            );
        }

        $messages = $validator->validate($this);
        if ($messages->count() > 0) {
            throw new ValidateException($messages);
        }
    }

    /**
     * @param MetaDataInterface $metaData
     * @param bool $exists
     * @param $identityField
     * @return bool
     */
    protected function preSave(MetaDataInterface $metaData, bool $exists, $identityField): bool
    {
        if (parent::preSave($metaData, $exists, $identityField)) {
            $this->password = new Security()->hash($this->password);

            return true;
        }

        return false;
    }
}
