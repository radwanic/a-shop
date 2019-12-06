<?php

namespace Tests\Unit;

use App\Shop\Orders\Order;
use App\Shop\Orders\OrderRepo;
use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use App\Shop\Products\ProductResource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itCreatesNewOrder()
    {
        $user = factory(User::class)->create();

        $productRepo = new ProductRepo();
        $orderRepo = new OrderRepo($productRepo, new ProductResource);

        $p1 = new Product([
            'name' => 'p1',
            'price' => 2,
        ]);

        $p2 = new Product([
            'name' => 'p1',
            'price' => 1.5,
        ]);

        $productRepo->store($p1);
        $productRepo->store($p2);

        $order = new Order([
            'user_id' => $user->id,
            'items' => [1, 2],
        ]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 3.5]);
        $this->assertDatabaseHas('order_products', ['order_id' => $order->getID(), 'product_id' => $p1->getID()]);
        $this->assertDatabaseHas('order_products', ['order_id' => $order->getID(), 'product_id' => $p2->getID()]);
    }

    /**
     * @test
     */
    public function itUpdatesExistingOrder()
    {
        $user = factory(User::class)->create();

        $productRepo = new ProductRepo();
        $orderRepo = new OrderRepo($productRepo, new ProductResource);

        $p1 = new Product([
            'name' => 'p1',
            'price' => 2,
        ]);

        $p2 = new Product([
            'name' => 'p1',
            'price' => 1.5,
        ]);

        $productRepo->store($p1);
        $productRepo->store($p2);

        $order = new Order([
            'user_id' => $user->id,
            'items' => [1, 2],
        ]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 3.5]);
        $this->assertDatabaseHas('order_products', ['order_id' => $order->getID(), 'product_id' => $p1->getID()]);
        $this->assertDatabaseHas('order_products', ['order_id' => $order->getID(), 'product_id' => $p2->getID()]);

        $p3 = new Product([
            'name' => 'p1',
            'price' => 100,
        ]);

        $productRepo->store($p3);

        $order->setAttribute('items', [$p3->getID()]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['id' => $order->getID(), 'user_id' => $user->id, 'total_price' => 100]);
        $this->assertDatabaseMissing('order_products', ['order_id' => $order->getID(), 'product_id' => $p1->getID()]);
        $this->assertDatabaseMissing('order_products', ['order_id' => $order->getID(), 'product_id' => $p2->getID()]);
        $this->assertDatabaseHas('order_products', ['order_id' => $order->getID(), 'product_id' => $p3->getID()]);
    }

    /**
     * @test
     */
    public function itDeletesOrder()
    {
        $user = factory(User::class)->create();

        $productRepo = new ProductRepo();
        $orderRepo = new OrderRepo($productRepo, new ProductResource);

        $p1 = new Product([
            'name' => 'p1',
            'price' => 2,
        ]);

        $p2 = new Product([
            'name' => 'p1',
            'price' => 1.5,
        ]);

        $productRepo->store($p1);
        $productRepo->store($p2);

        $order = new Order([
            'user_id' => $user->id,
            'items' => [1, 2],
        ]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['id' => $order->getID()]);

        $orderRepo->delete(1);

        $this->assertDatabaseMissing('orders', ['id' => $order->getID()]);
    }

    /**
     * @test
     */
    public function itValidatesRequiredData()
    {
        $repo = new OrderRepo(new ProductRepo(), new ProductResource());

        $this->expectException(\InvalidArgumentException::class);

        $order = new Order([]);

        $repo->store($order);

        $this->assertDatabaseMissing('orders', ['id' => 1]);
    }
}
