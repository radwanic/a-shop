<?php

namespace App\Shop\Products;

use App\Shop\Model;

/**
 * Class Product
 * @package App\Shop\Products
 */
class Product extends Model
{
    /**
     * @var array
     */
    protected $acceptableAttributes = ['name', 'price', 'bundle_id', 'discount_id'];

    /**
     * @return array
     */
    public function requiredAttributes(): array
    {
        return ['name', 'price'];
    }

    /**
     * Product constructor.
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
}
