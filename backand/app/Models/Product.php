<?php

declare(strict_types=1);

namespace App\Models;

use Core\AbstractModel;
use Core\Exceptions\ValidateException;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Numericality;
use Phalcon\Filter\Validation\Validator\StringLength;

/**
 * @method int getId()
 * @method void setId(int $value)
 * @method string getName()
 * @method void setName(string $value)
 * @method string getDescription()
 * @method void setDescription(string $value)
 * @method float getPrice()
 * @method void setPrice(float $value)
 * @method bool getInStock()
 * @method bool setInStock(bool $value)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method void setUpdatedAt(string $value)
 */
class Product extends AbstractModel
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?float $price = null;
    public ?bool $in_stock = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function initialize(): void
    {
        $this->setSource('products');
        $this->setPrimaryKey('id');

        $this->hasManyToMany(
            'id',
            CategoryProduct::class,
            'product_id',
            'category_id',
            Category::class,
            'id'
        );

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
        $messages = new Validation()
            ->add(
                'name',
                new PresenceOf([
                    'message' => 'Название товара обязательно',
                    'cancelOnFail' => true
                ])
            )->add(
                'name',
                new StringLength([
                    'min' => 2,
                    'max' => 255,
                    'messageMinimum' => 'Название должно содержать минимум 2 символа',
                    'messageMaximum' => 'Название не должно превышать 255 символов'
                ])
            )->add(
                'description',
                new StringLength([
                    'max' => 1000,
                    'messageMaximum' => 'Описание не должно превышать 1000 символов',
                    'allowEmpty' => true
                ])
            )->add(
                'price',
                new PresenceOf([
                    'message' => 'Цена товара обязательна',
                    'cancelOnFail' => true
                ])
            )->add(
                'price',
                new Numericality([
                    'message' => 'Цена должна быть числом',
                    'min' => 0,
                    'minMessage' => 'Цена не может быть отрицательной'
                ])
            )->validate($this);

        if ($messages->count() > 0) {
            throw new ValidateException($messages);
        }
    }
}
