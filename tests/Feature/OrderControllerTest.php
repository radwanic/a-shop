<?php

namespace Tests\Feature;

use App\Shop\Discounts\Discount;
use App\Shop\Discounts\DiscountRepo;
use App\Shop\Orders\Order;
use App\Shop\Orders\OrderRepo;
use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use App\Shop\Products\ProductResource;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testNewOrderCreation()
    {
        $productRepo = new ProductRepo();
        $user = factory(User::class)->create();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->postJson('/api/orders', [
            'user_id' => $user->id,
            'items' => [$product->getID()]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 2.4]);
    }

    /**
     * @return void
     */
    public function testOrderUpdating()
    {
        $productRepo = new ProductRepo();
        $orderRepo = new OrderRepo($productRepo, new ProductResource);

        $user = factory(User::class)->create();

        $product1 = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product1);

        $order = new Order([
            'user_id' => $user->id,
            'items' => [1],
        ]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 2.4]);

        $product2 = new Product([
            'name' => 'another product',
            'price' => 1.2,
        ]);

        $productRepo->store($product2);

        $response = $this->putJson('/api/orders/' . $order->getID(), [
            'items' => [$product1->getID(), $product2->getID()]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 3.6]);
    }

    /**
     * @return void
     */
    public function testOrderTotalPriceCalculatedWithDiscounts()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();
        $orderRepo = new OrderRepo($productRepo, new ProductResource);

        $user = factory(User::class)->create();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount',
        ]);

        $discountRepo->store($discount);

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $order = new Order([
            'user_id' => $user->id,
            'items' => [1],
        ]);

        $orderRepo->store($order);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'total_price' => 1.4]);
    }
}
