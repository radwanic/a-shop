<?php

namespace Tests\Unit;

use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itCreatesNewProduct()
    {
        $repo = new ProductRepo();

        $mobile = new Product([
            'name' => 'mobile',
            'price' => 2.4,
        ]);

        $repo->store($mobile);

        $this->assertDatabaseHas('products', ['name' => 'mobile', 'price' => 2.4]);

        $mobileCover = new Product();

        $mobileCover->setAttribute('name', 'cover');
        $mobileCover->setAttribute('price', 0.6);

        $repo->store($mobileCover);

        $this->assertDatabaseHas('products', ['name' => 'cover', 'price' => 0.6]);
    }

    /**
     * @test
     */
    public function itUpdatesExistingProduct()
    {
        $repo = new ProductRepo();

        $mobile = new Product([
            'name' => 'mobile',
            'price' => 2.4,
        ]);

        $repo->store($mobile);

        $this->assertDatabaseHas('products', ['id' => 1, 'name' => 'mobile', 'price' => 2.4]);

        $mobile->setID(1);
        $mobile->setAttribute('price', 4.6);

        $repo->store($mobile);

        $this->assertDatabaseHas('products', ['id' => 1, 'name' => 'mobile', 'price' => 4.6]);
    }

    /**
     * @test
     */
    public function itDeletesProduct()
    {
        $repo = new ProductRepo();

        $mobile = new Product([
            'name' => 'mobile',
            'price' => 2.4,
        ]);

        $repo->store($mobile);

        $this->assertDatabaseHas('products', ['id' => 1, 'name' => 'mobile', 'price' => 2.4]);

        $repo->delete(1);

        $this->assertDatabaseMissing('products', ['id' => 1]);
    }

    /**
     * @test
     */
    public function itValidatesRequiredData()
    {
        $repo = new ProductRepo();

        $this->expectException(\InvalidArgumentException::class);

        $product = new Product([]);

        $repo->store($product);

        $this->assertDatabaseMissing('products', ['id' => 1]);
    }
}
