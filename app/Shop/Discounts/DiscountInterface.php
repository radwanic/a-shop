<?php

namespace App\Shop\Discounts;

/**
 * Interface DiscountInterface
 * @package App\Shop\Discounts
 */
interface DiscountInterface
{
    /**
     * @param $amount
     * @return mixed
     */
    public function apply($amount);
}
