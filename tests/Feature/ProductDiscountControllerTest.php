<?php

namespace Tests\Feature;

use App\Shop\Discounts\Discount;
use App\Shop\Discounts\DiscountRepo;
use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testProductDiscountAttachment()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount'
        ]);

        $discountRepo->store($discount);

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', ['id' => $product->getID(), 'discount_id' => $discount->getID()]);
    }

    /**
     * @return void
     */
    public function testProductDiscountDetachment()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount'
        ]);

        $discountRepo->store($discount);

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->putJson('/api/detach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->getID(), 'discount_id' => $discount->getID()]);
    }

    /**
     * @return void
     */
    public function testFixedDiscountApplyingOnProduct()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount'
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

        $response = $this->getJson('/api/products/' . $product->getID())->decodeResponseJson();

        $this->assertEquals(1.4, $response['price']);
    }

    /**
     * @return void
     */
    public function testPercentageDiscountApplyingOnProduct()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 20,
            'type' => 'PercentDiscount'
        ]);

        $discountRepo->store($discount);

        $product = new Product([
            'name' => 'product',
            'price' => 5,
        ]);

        $productRepo->store($product);

        $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $response = $this->getJson('/api/products/' . $product->getID())->decodeResponseJson();

        $this->assertEquals(4, $response['price']);
    }
}
