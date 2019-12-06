<?php

namespace App\Shop\Discounts;

/**
 * Class DiscountResource
 * @package App\Shop\Discounts
 */
class DiscountResource
{
    /**
     * @param $items
     * @return array
     */
    public function collection($items) {
        return $this->format($items);
    }

    /**
     * @param $item
     * @return mixed
     */
    public function make($item) {
        return @$this->format([$item])[0];
    }

    /**
     * @param $collection
     * @return array
     */
    private function format($collection) {
        return array_values($collection);
    }
}
