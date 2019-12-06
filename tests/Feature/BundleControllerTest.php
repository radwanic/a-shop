<?php

namespace Tests\Feature;

use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BundleControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testNewBundleCreation()
    {
        $response = $this->postJson('/api/bundles', [
            'name' => 'bundle',
            'price' => 1,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2,
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', ['name' => 'bundle', 'price' => 1]);
        $this->assertDatabaseHas('products', ['name' => 'product', 'price' => 2]);
    }

    /**
     * @return void
     */
    public function testBundleUpdating()
    {
        $productRepo = new ProductRepo();

        $bundle = new Product([
            'name' => 'bundle',
            'price' => 2,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2.4
                ]
            ]
        ]);

        $productRepo->store($bundle);

        $response = $this->putJson('/api/bundles/' . $bundle->getID(), [
            'name' => 'new name',
            'price' => 2.2,
            'items' => [
                [
                    'name' => 'new product',
                    'price' => 2.8,
                ]
            ]
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $bundle->getID(), 'name' => 'bundle', 'price' => 2]);

        $this->assertDatabaseHas('products', ['id' => $bundle->getID(), 'name' => 'new name', 'price' => 2.2]);
        $this->assertDatabaseHas('products',
            ['bundle_id' => $bundle->getID(), 'name' => 'new product', 'price' => 2.8]);
    }

    /**
     * @return void
     */
    public function testBundleListing()
    {
        $productRepo = new ProductRepo();

        $bundle1 = new Product([
            'name' => 'bundle',
            'price' => 2,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2.4,
                ]
            ]
        ]);

        $bundle2 = new Product([
            'name' => 'bundle',
            'price' => 2,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2.4,
                ]
            ]
        ]);

        $productRepo->store($bundle1);
        $productRepo->store($bundle2);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $this->assertCount(2, $response->decodeResponseJson());
    }

    /**
     * @return void
     */
    public function testBundleDisplaying()
    {
        $productRepo = new ProductRepo();

        $bundle = new Product([
            'name' => 'bundle',
            'price' => 2,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2.4,
                ]
            ]
        ]);

        $productRepo->store($bundle);

        $response = $this->getJson('/api/products/' . $bundle->getID());

        $response->assertStatus(200);

        $decodedResponse = $response->decodeResponseJson();

        $this->assertEquals('bundle', $decodedResponse['name']);
        $this->assertEquals(2, $decodedResponse['price']);
    }

    /**
     * @return void
     */
    public function testBundleDeletion()
    {
        $productRepo = new ProductRepo();

        $bundle = new Product([
            'name' => 'bundle',
            'price' => 2,
            'items' => [
                [
                    'name' => 'product',
                    'price' => 2.4,
                ]
            ]
        ]);

        $productRepo->store($bundle);

        $response = $this->deleteJson('/api/products/' . $bundle->getID());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $bundle->getID()]);
    }
}
