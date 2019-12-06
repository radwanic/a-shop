<?php

namespace Tests\Feature;

use App\Shop\Discounts\DiscountRepo;
use App\Shop\Discounts\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testNewDiscountCreation()
    {
        $response = $this->postJson('/api/discounts', [
            'name' => 'discount',
            'amount' => 1,
            'type' => 'FixedDiscount'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('discounts', ['name' => 'discount', 'amount' => 1, 'type' => 'FixedDiscount']);
    }

    /**
     * @return void
     */
    public function testDiscountUpdating()
    {
        $discountRepo = new DiscountRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 1,
            'type' => 'PercentDiscount'
        ]);

        $discountRepo->store($discount);

        $response = $this->putJson('/api/discounts/' . $discount->getID(), [
            'name' => 'new name',
            'amount' => 3,
            'type' => 'FixedDiscount'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('discounts', ['id' => $discount->getID(), 'name' => 'new name', 'amount' => 3, 'type' => 'FixedDiscount']);
    }

    /**
     * @return void
     */
    public function testDiscountListing()
    {
        $discountRepo = new DiscountRepo();

        $discount1 = new Discount([
            'name' => 'discount1',
            'amount' => 2,
            'type' => 'FixedDiscount',
        ]);

        $discount2 = new Discount([
            'name' => 'discount2',
            'amount' => 4,
            'type' => 'PercentDiscount',
        ]);

        $discountRepo->store($discount1);
        $discountRepo->store($discount2);

        $response = $this->getJson('/api/discounts');

        $response->assertStatus(200);

        $this->assertCount(2, $response->decodeResponseJson());
    }

    /**
     * @return void
     */
    public function testDiscountDisplaying()
    {
        $discountRepo = new DiscountRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 2,
            'type' => 'FixedDiscount'
        ]);

        $discountRepo->store($discount);

        $response = $this->getJson('/api/discounts/' . $discount->getID());

        $response->assertStatus(200);

        $decodedResponse = $response->decodeResponseJson();

        $this->assertEquals('discount', $decodedResponse['name']);
        $this->assertEquals(2, $decodedResponse['amount']);
    }

    /**
     * @return void
     */
    public function testDiscountDeletion()
    {
        $discountRepo = new DiscountRepo();

        $discount = new Discount([
            'name' => 'discount',
            'amount' => 2.4,
            'type' => 'FixedDiscount'
        ]);

        $discountRepo->store($discount);

        $response = $this->deleteJson('/api/discounts/' . $discount->getID());

        $response->assertStatus(200);

        $this->assertDatabaseMissing('discounts', ['id' => $discount->getID()]);
    }
}
