<?php

namespace Tests\Unit;

use App\Shop\Products\Bundle;
use App\Shop\Products\ProductRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BundlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itCreatesNewBundle()
    {
        $repo = new ProductRepo();

        $bundle = new Bundle([
            'name' => 'mobile and cover',
            'price' => 2.8,
            'items' => [
                [
                    'name' => 'mobile',
                    'price' => 1.4,
                ],
                [
                    'name' => 'cover',
                    'price' => 0.6,
                ],
            ]
        ]);

        $repo->store($bundle);

        $this->assertDatabaseHas('products', ['name' => 'mobile and cover', 'bundle_id' => null, 'price' => 2.8]);
        $this->assertDatabaseHas('products', ['name' => 'mobile', 'bundle_id' => $bundle->getID(), 'price' => 1.4]);
        $this->assertDatabaseHas('products', ['name' => 'cover', 'bundle_id' => $bundle->getID(), 'price' => 0.6]);
    }

    /**
     * @test
     */
    public function itUpdatesExistingBundle()
    {
        $repo = new ProductRepo();

        $bundle = new Bundle([
            'name' => 'mobile and cover',
            'price' => 2.8,
            'items' => [
                [
                    'name' => 'mobile',
                    'price' => 1.4,
                ],
                [
                    'name' => 'cover',
                    'price' => 0.6,
                ],
            ]
        ]);

        $repo->store($bundle);

        $bundle->setAttribute('name', 'mobile and headset');
        $bundle->setAttribute('price', 2);
        $bundle->setAttribute('items', [
            [
                'name' => 'mobile',
                'price' => 2,
            ],
            [
                'name' => 'headset',
                'price' => 0.2,
            ]
        ]);

        $repo->store($bundle);

        $this->assertDatabaseHas('products', ['id' => $bundle->getID(), 'name' => 'mobile and headset', 'bundle_id' => null, 'price' => 2]);
        $this->assertDatabaseHas('products', ['name' => 'mobile', 'bundle_id' => $bundle->getID(), 'price' => 2]);
        $this->assertDatabaseHas('products', ['name' => 'headset', 'bundle_id' => $bundle->getID(), 'price' => 0.2]);

        $this->assertDatabaseHas('products', ['name' => 'cover']);
        $this->assertDatabaseMissing('products', ['name' => 'cover', 'bundle_id' => $bundle->getID()]);
    }

    /**
     * @test
     */
    public function itValidatesRequiredData()
    {
        $repo = new ProductRepo();

        $this->expectException(\InvalidArgumentException::class);

        $product = new Bundle([]);

        $repo->store($product);

        $this->assertDatabaseMissing('products', ['id' => 1]);
    }
}
