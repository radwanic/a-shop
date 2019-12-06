<?php

namespace App\Shop\Discounts;

use App\Shop\Model;

/**
 * Class Discount
 * @package App\Shop\Discounts
 */
class Discount extends Model
{
    /**
     * @var array
     */
    protected $acceptableAttributes = ['name', 'amount', 'type'];

    /**
     * Discount constructor.
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
        return ['name', 'amount', 'type'];
    }
}
