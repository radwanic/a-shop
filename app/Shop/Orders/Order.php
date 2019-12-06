<?php

namespace App\Shop\Orders;

use App\Shop\Model;

/**
 * Class Order
 * @package App\Shop\Orders
 */
class Order extends Model
{
    /**
     * @var array
     */
    protected $acceptableAttributes = ['user_id', 'total_price', 'items'];

    /**
     * Order constructor.
     * @param array $props
     */
    public function __construct($props = [])
    {
        if(isset($props['id'])) {
            $this->setID($props['id']);
        }

        //Allow only acceptable attributes
        $this->attributes = array_intersect_key($props, array_flip($this->acceptableAttributes));
    }

    /**
     * @return array
     */
    public function requiredAttributes(): array
    {
        return ['user_id', 'total_price', 'items'];
    }
}
