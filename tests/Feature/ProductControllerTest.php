<?php

namespace Tests\Feature;

use App\Shop\Discounts\Discount;
use App\Shop\Discounts\DiscountRepo;
use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testNewProductCreation()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'product',
            'price' => 1
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', ['name' => 'product', 'price' => 1]);
    }

    /**
     * @return void
     */
    public function testProductUpdating()
    {
        $productRepo = new ProductRepo();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->putJson('/api/products/' . $product->getID(), [
            'name' => 'new name',
            'price' => 3
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->getID(), 'name' => 'product', 'price' => 2.4]);
        $this->assertDatabaseHas('products', ['id' => $product->getID(), 'name' => 'new name', 'price' => 3]);
    }

    /**
     * @return void
     */
    public function testProductListing()
    {
        $productRepo = new ProductRepo();

        $product1 = new Product([
            'name' => 'product1',
            'price' => 2,
        ]);

        $product2 = new Product([
            'name' => 'product2',
            'price' => 4,
        ]);

        $productRepo->store($product1);
        $productRepo->store($product2);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $this->assertCount(2, $response->decodeResponseJson());
    }


    /**
     * @return void
     */
    public function testProductListingWithDiscountPrice()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $product1 = new Product([
            'name' => 'product1',
            'price' => 2,
        ]);

        $product2 = new Product([
            'name' => 'product2',
            'price' => 7,
        ]);

        $productRepo->store($product1);
        $productRepo->store($product2);

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 50,
            'type' => 'PercentDiscount',
        ]);

        $discountRepo->store($discount);

        $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product2->getID()
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $results = $response->decodeResponseJson();

        $this->assertCount(2, $results);

        $this->assertEquals(2, $results[0]['price']);
        $this->assertEquals(3.5, $results[1]['price']);
    }

    /**
     * @return void
     */
    public function testProductDisplaying()
    {
        $productRepo = new ProductRepo();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->getJson('/api/products/' . $product->getID());

        $response->assertStatus(200);

        $decodedResponse = $response->decodeResponseJson();

        $this->assertEquals('product', $decodedResponse['name']);
        $this->assertEquals(2.4, $decodedResponse['price']);
    }

    /**
     * @return void
     */
    public function testDisplayingProductPriceWithFixedDiscountCalculated()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount',
        ]);

        $discountRepo->store($discount);

        $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $response = $this->getJson('/api/products/' . $product->getID());

        $response->assertStatus(200);

        $decodedResponse = $response->decodeResponseJson();

        $this->assertEquals('product', $decodedResponse['name']);
        $this->assertEquals(1.4, $decodedResponse['price']);
    }

    /**
     * @return void
     */
    public function testDisplayingProductPriceWithPercentDiscountCalculated()
    {
        $discountRepo = new DiscountRepo();
        $productRepo = new ProductRepo();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 50,
            'type' => 'PercentDiscount',
        ]);

        $discountRepo->store($discount);

        $this->putJson('/api/attach-discount', [
            'discount_id' => $discount->getID(),
            'product_id' => $product->getID()
        ]);

        $response = $this->getJson('/api/products/' . $product->getID());

        $response->assertStatus(200);

        $decodedResponse = $response->decodeResponseJson();

        $this->assertEquals('product', $decodedResponse['name']);
        $this->assertEquals(1.2, $decodedResponse['price']);
    }

    /**
     * @return void
     */
    public function testProductDeletion()
    {
        $productRepo = new ProductRepo();

        $product = new Product([
            'name' => 'product',
            'price' => 2.4,
        ]);

        $productRepo->store($product);

        $response = $this->deleteJson('/api/products/' . $product->getID());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->getID()]);
    }
}
