<?php

namespace App\Shop\Orders;

use App\Foodics\AbstractRepo;
use App\Shop\Products\ProductRepo;
use App\Shop\Products\ProductResource;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderRepo
 * @package App\Shop\Orders
 */
class OrderRepo extends AbstractRepo
{
    /**
     * @var ProductRepo
     */
    private $productsRepo;
    /**
     * @var ProductResource
     */
    private $productsResource;

    /**
     * OrderRepo constructor.
     * @param $productsRepo
     * @param $productsResource
     */
    public function __construct(ProductRepo $productsRepo, ProductResource $productsResource)
    {
        $this->productsRepo = $productsRepo;
        $this->productsResource = $productsResource;
    }

    /**
     * @param \App\Shop\Orders\Order $order
     * @return mixed|null
     */
    public function store($order)
    {
        if(!$itemIds = $order->getAttribute('items'))  {
            throw new \InvalidArgumentException('order must have one or more items');
        }

        $items = $this->productsResource->collection(
            $this->productsRepo->getAll($itemIds)
        );

        if (count($items) != count($itemIds)) {
            throw new \InvalidArgumentException('order have one or more invalid items');
        }

        $order->setAttribute('total_price', $this->calculateOrderPrice($items));

        if ($order->getID()) {
            $this->update($order);
        } else {
            $this->validate($order);

            $this->insert($order);
        }

        $this->detachAllItems($order);

        foreach ($itemIds as $itemId) {
            $this->attachItem($order, $itemId);
        }

        return $this->get($order->getID());
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function get($id)
    {
        $record = DB::selectOne('SELECT * FROM orders WHERE id = ?', [$id]);

        if (!$record) {
            return null;
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
            "DELETE FROM orders WHERE id = ?", [$id]
        );
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $results = DB::select(
            "SELECT * FROM orders"
        );

        return $this->toArray($results);
    }

    /**
     * @param \App\Shop\Orders\Order $order
     */
    private function insert($order)
    {
        DB::statement(
            "INSERT INTO orders (user_id, total_price) VALUES (?, ?)",
            [
                $order->getAttribute('user_id'),
                $order->getAttribute('total_price')
            ]
        );

        $order->setID(DB::getPdo()->lastInsertId());
    }

    /**
     * @param \App\Shop\Orders\Order $order
     */
    private function update($order)
    {
        DB::statement(
            "UPDATE orders SET total_price = ? WHERE id = ?",
            [
                $order->getAttribute('total_price'),
                $order->getID()
            ]
        );
    }

    /**
     * @param Order $order
     */
    private function detachAllItems(Order $order)
    {
        DB::statement(
            "DELETE FROM order_products WHERE order_id = ?",
            [
                $order->getID()
            ]
        );
    }

    /**
     * @param Order $order
     * @param $relatedId
     */
    private function attachItem(Order $order, $relatedId)
    {
        DB::statement(
            "INSERT INTO order_products (order_id, product_id) VALUES (?, ?)",
            [
                $order->getID(),
                $relatedId
            ]
        );
    }

    /**
     * @param $items
     * @return float|int
     */
    private function calculateOrderPrice($items)
    {
        return array_sum(array_column($items, 'price'));
    }
}
