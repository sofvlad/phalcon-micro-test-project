<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ProductCategoriesSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        // Категории (6 штук)
        $categories = [
            ['name' => 'Электроника', 'code' => 'electronics'],
            ['name' => 'Одежда', 'code' => 'clothing'],
            ['name' => 'Для дома', 'code' => 'home'],
            ['name' => 'Книги', 'code' => 'books'],
            ['name' => 'Еда', 'code' => 'eat'],
            ['name' => 'Игры', 'code' => 'games'],
        ];

        $table = $this->table('categories');
        foreach ($categories as $cat) {
            $table->insert($cat)->save();
        }

        // Продукты (500 шт: 450 in_stock = 1, 50 in_stock = 0)
        $adjectives = ['Premium', 'Basic', 'Pro', 'Ultra', 'Light', 'Heavy', 'Smart', 'Classic', 'Modern', 'Eco'];
        $nouns = ['Gadget', 'Device', 'Tool', 'Item', 'Product', 'Accessory', 'Component', 'Set', 'Pack', 'Thing'];

        $productsTable = $this->table('products');
        for ($i = 1; $i <= 500; $i++) {
            $adj = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            $name = "$adj $noun " . rand(100, 999);
            $price = rand(5, 1000);
            $inStock = $i < 450;

            $productsTable->insert([
                'name' => $name,
                'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. "
                    . "Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, "
                    . "when an unknown printer took a galley of type and scrambled it to make a type specimen book.",
                'price' => $price,
                'in_stock' => $inStock,
            ])->save();
        }

        // Связи product_categories (каждому продукту 1-3 случайные категории)
        $productIds = array_column($this->fetchAll('SELECT id FROM products'), 'id');
        $categoryIds = array_column($this->fetchAll('SELECT id FROM categories'), 'id');

        $cpTable = $this->table('category_products');
        foreach ($productIds as $productId) {
            $numCats = rand(1, 3);
            $randomCatKeys = (array) array_rand($categoryIds, $numCats);
            foreach ($randomCatKeys as $key) {
                $cpTable->insert([
                    'product_id'  => $productId,
                    'category_id' => $categoryIds[$key],
                ])->save();
            }
        }
    }
}
