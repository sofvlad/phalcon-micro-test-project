<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use Core\AbstractRepository;
use Phalcon\Di\DiInterface;

class CategoryRepository extends AbstractRepository
{
    public function __construct(protected DiInterface $di)
    {
        parent::__construct(Category::class, $di->get('modelsManager'));
    }
}
