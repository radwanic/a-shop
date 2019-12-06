<?php

namespace App\Shop\Orders;

/**
 * Class OrderResource
 * @package App\Shop\Orders
 */
class OrderResource
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
