<?php

namespace App\Shop\Products;

/**
 * Class ProductResource
 * @package App\Shop\Products
 */
class ProductResource
{
    /**
     * @param $items
     * @return array
     */
    public function collection($items) {
        $collection = [];

        foreach ($items as $item) {
            $collection[$item['id']] = $item;
        }

        return $this->format($collection);
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
        foreach ($collection as &$item) {
            $bundleId = @$item['bundle_id'];
            $discountId = @$item['discount_id'];

            if($bundleId && @$collection[$bundleId]) {
                $collection[$bundleId]['items'][] = $item;
            }

            if($discountId) {
                $discountClass = "App\\Shop\\Discounts\\" . $item['discount_type'];

                if(class_exists($discountClass)) {
                    $discount = new $discountClass(['amount' => $item['discount_amount']]);

                    $item['price'] = $discount->apply($item['price']);
                }
            }
        }

        return array_values($collection);
    }
}
