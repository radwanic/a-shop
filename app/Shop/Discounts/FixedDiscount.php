<?php

namespace App\Shop\Discounts;

/**
 * Class FixedDiscount
 * @package App\Shop\Discounts
 */
class FixedDiscount extends Discount implements DiscountInterface
{
    /**
     * @param $amount
     * @return mixed
     */
    public function apply($amount)
    {
        return $amount - $this->getAttribute('amount');
    }
}
