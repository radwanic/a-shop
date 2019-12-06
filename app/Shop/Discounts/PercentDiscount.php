<?php

namespace App\Shop\Discounts;

/**
 * Class PercentDiscount
 * @package App\Shop\Discounts
 */
class PercentDiscount extends Discount implements DiscountInterface
{
    /**
     * @param $amount
     * @return mixed
     */
    public function apply($amount)
    {
        return $amount - ($amount * $this->getAttribute('amount') / 100);
    }
}
