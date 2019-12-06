<?php

namespace App\Shop\Products;

/**
 * Class Bundle
 * @package App\Shop\Products
 */
class Bundle extends Product
{
    /**
     * @var array
     */
    protected $acceptableAttributes = ['name', 'price', 'items'];

    /**
     * @return array
     */
    public function requiredAttributes(): array
    {
        return ['name', 'price', 'items'];
    }
}
