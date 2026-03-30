<?php

declare(strict_types=1);

namespace App\Models;

use Core\AbstractModel;
use Core\Exceptions\ValidateException;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\StringLength;

/**
 * @method int getId()
 * @method void setId(int $value)
 * @method string getName()
 * @method void setName(string $value)
 * @method string getDescription()
 * @method void setDescription(string $value)
 * @method string getCreatedAt()
 * @method void setCreatedAt(string $value)
 */
class CategoryProduct extends AbstractModel
{
    public ?int $product_id = null;
    public ?int $category_id = null;

    public function initialize(): void
    {
        $this->setSource('category_products');

        $this->hasOne(
            'category_id',
            Category::class,
            'id'
        );
        $this->hasOne(
            'product_id',
            Product::class,
            'id'
        );
    }
}
