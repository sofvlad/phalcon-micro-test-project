<?php

declare(strict_types=1);

namespace App\Models;

use Core\AbstractModel;
use Core\Exceptions\ValidateException;
use Phalcon\Filter\Validation\Validator\Uniqueness;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;

/**
 * @method int getId()
 * @method void setId(int $value)
 * @method string getCode()
 * @method void setCode(string $value)
 * @method string getName()
 * @method void setName(string $value)
 * @method string getDescription()
 * @method void setDescription(string $value)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $value)
 */
class Category extends AbstractModel
{
    public ?int $id = null;
    public ?string $code = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $created_at = null;

    public function initialize(): void
    {
        $this->setSource('categories');

        $this->hasManyToMany(
            'id',
            CategoryProduct::class,
            'category_id',
            'product_id',
            Product::class,
            'id'
        );

        $this->addBehavior(
            new Timestampable([
                'beforeCreate' => [
                    'field'  => 'created_at',
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
                'name',
                new PresenceOf([
                    'message' => 'Название категории обязательно',
                    'cancelOnFail' => true
                ])
            )->add(
                'name',
                new StringLength([
                    'min' => 2,
                    'max' => 100,
                    'messageMinimum' => 'Название должно содержать минимум 2 символа',
                    'messageMaximum' => 'Название не должно превышать 100 символов'
                ])
            )->add(
                'description',
                new StringLength([
                    'max' => 65535,
                    'messageMaximum' => 'Описание не должно превышать 65535 символов',
                    'allowEmpty' => true
                ])
            );

        if (empty($this->getId())) {
            $validator->add(
                'name',
                new Uniqueness(
                    [
                        "model" => new Category(),
                        "attribute" => "name",
                        'message' => 'Категория с таким названием уже есть',
                    ]
                )
            )->add(
                'code',
                new Uniqueness(
                    [
                        "model" => new Category(),
                        "attribute" => "code",
                        'message' => 'Категория с таким кодом уже есть',
                    ]
                )
            );
        }

        $messages = $validator->validate($this);
        if ($messages->count() > 0) {
            throw new ValidateException($messages);
        }
    }
}
