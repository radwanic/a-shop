<?php

namespace App\Shop\Products;

use App\Foodics\AbstractRepo;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductRepo
 * @package App\Shop\Products
 */
class ProductRepo extends AbstractRepo
{
    /**
     * @param \App\Shop\Products\Product $product
     * @return mixed|null
     */
    public function store($product)
    {
        if ($product->getID()) {
            $this->update($product);
        } else {
            $this->validate($product);

            $this->insert($product);
        }

        if ($product->getAttribute('items')) {
            $this->detachAllItems($product);

            foreach ($product->getAttribute('items') as $item) {
                if (is_array($item)) {
                    $item = new Product($item);
                }

                $this->attachItem($product, $item);
            }
        }

        return $this->get($product->getID());
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function get($id)
    {
        $record = DB::selectOne('SELECT products.*, discounts.id AS discount_id, discounts.type AS discount_type, discounts.name AS discount_name, discounts.amount AS discount_amount FROM products LEFT JOIN discounts ON products.discount_id = discounts.id WHERE products.id = ?',
            [$id]);

        if (!$record) {
            return null;
        }

        $items = DB::select(
            "SELECT products.*, discounts.id AS discount_id, discounts.type AS discount_type, discounts.name AS discount_name, discounts.amount AS discount_amount FROM products LEFT JOIN discounts ON products.discount_id=discounts.id WHERE products.bundle_id = ?",
            [$id]
        );

        if ($items) {
            $record->items = $this->toArray($items);
        }

        return $this->toArray($record);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return DB::statement(
            "DELETE FROM products WHERE id = ?", [$id]
        );
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function getAll(array $ids = [])
    {
        $whereIn = count($ids) ? "WHERE products.id IN (" . implode(',', $ids) . ")" : "";

        $results = DB::select(
            "SELECT products.*, discounts.id AS discount_id, discounts.type AS discount_type, discounts.name AS discount_name, discounts.amount AS discount_amount FROM products LEFT JOIN discounts ON products.discount_id=discounts.id $whereIn"
        );

        return $this->toArray($results);
    }

    /**
     * @param Product $product
     */
    private function insert($product)
    {
        DB::statement(
            "INSERT INTO products (name, price, bundle_id) VALUES (?, ?, ?)",
            [
                $product->getAttribute('name'),
                $product->getAttribute('price'),
                $product->getAttribute('bundle_id')
            ]
        );

        $product->setID(DB::getPdo()->lastInsertId());
    }

    /**
     * @param Product $product
     */
    private function update($product)
    {
        DB::statement(
            "UPDATE products SET name = ?, price = ?, discount_id = ? WHERE id = ?",
            [
                $product->getAttribute('name'),
                $product->getAttribute('price'),
                $product->getAttribute('discount_id'),
                $product->getID()
            ]
        );
    }

    /**
     * @param Product $product
     */
    private function detachAllItems(Product $product)
    {
        DB::statement(
            "UPDATE products SET bundle_id = null WHERE bundle_id = ?",
            [
                $product->getID()
            ]
        );
    }

    /**
     * @param Product $product
     * @param Product $related
     */
    private function attachItem(Product $product, Product $related)
    {
        $related->setAttribute('bundle_id', $product->getID());

        $this->store($related);
    }
}
