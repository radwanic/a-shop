<?php

namespace Tests\Unit;

use App\Shop\Discounts\Discount;
use App\Shop\Discounts\DiscountRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itCreatesNewDiscount()
    {
        $repo = new DiscountRepo();

        $discount1 = new Discount([
            'name' => 'discount1',
            'amount' => 2,
            'type' => 'FixedDiscount',
        ]);

        $repo->store($discount1);

        $this->assertDatabaseHas('discounts', ['name' => 'discount1', 'amount' => 2, 'type' => 'FixedDiscount']);

        $discount2 = new Discount();

        $discount2->setAttribute('name', 'discount2');
        $discount2->setAttribute('amount', 10);
        $discount2->setAttribute('type', 'PercentDiscount');

        $repo->store($discount2);

        $this->assertDatabaseHas('discounts', ['name' => 'discount2', 'amount' => 10, 'type' => 'PercentDiscount']);
    }

    /**
     * @test
     */
    public function itUpdatesExistingDiscount()
    {
        $repo = new DiscountRepo();

        $discount1 = new Discount([
            'name' => 'discount1',
            'amount' => 2,
            'type' => 'FixedDiscount',
        ]);

        $repo->store($discount1);

        $this->assertDatabaseHas('discounts', ['name' => 'discount1', 'amount' => 2, 'type' => 'FixedDiscount']);

        $discount1->setID(1);
        $discount1->setAttribute('amount', 4);

        $repo->store($discount1);

        $this->assertDatabaseHas('discounts', ['name' => 'discount1', 'amount' => 4, 'type' => 'FixedDiscount']);
    }

    /**
     * @test
     */
    public function itDeletesDiscount()
    {
        $repo = new DiscountRepo();

        $discount1 = new Discount([
            'name' => 'discount1',
            'amount' => 2,
            'type' => 'FixedDiscount',
        ]);

        $repo->store($discount1);

        $this->assertDatabaseHas('discounts', ['id' => 1, 'name' => 'discount1', 'amount' => 2, 'type' => 'FixedDiscount']);

        $repo->delete(1);

        $this->assertDatabaseMissing('discounts', ['id' => 1]);
    }

    /**
     * @test
     */
    public function itValidatesRequiredData()
    {
        $repo = new DiscountRepo();

        $this->expectException(\InvalidArgumentException::class);

        $discount = new Discount([]);

        $repo->store($discount);

        $this->assertDatabaseMissing('discounts', ['id' => 1]);
    }

    /**
     * @test
     */
    public function itValidatesDiscountType()
    {
        $repo = new DiscountRepo();

        $this->expectException(\InvalidArgumentException::class);

        $discount1 = new Discount([
            'name' => 'discount1',
            'amount' => 2,
            'type' => 'Incorrect',
        ]);

        $repo->store($discount1);

        $this->assertDatabaseMissing('discounts', ['id' => 1]);
    }
}
