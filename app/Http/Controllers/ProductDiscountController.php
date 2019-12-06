<?php

namespace App\Http\Controllers;


use App\Shop\Discounts\DiscountRepo;
use App\Shop\Products\Product;
use App\Shop\Products\ProductRepo;

/**
 * Class ProductDiscountController
 * @package App\Http\Controllers
 */
class ProductDiscountController extends Controller
{
    /**
     * @var ProductRepo
     */
    private $productRepo;
    /**
     * @var DiscountRepo
     */
    private $discountRepo;

    /**
     * ProductDiscountController constructor.
     * @param ProductRepo $productRepo
     * @param DiscountRepo $discountRepo
     */
    function __construct(ProductRepo $productRepo, DiscountRepo $discountRepo)
    {
        $this->productRepo = $productRepo;
        $this->discountRepo = $discountRepo;
    }

    /**
     * @return mixed|null
     */
    public function attach()
    {
        $product = $this->productRepo->get(request('product_id'));
        $discount = $this->discountRepo->get(request('discount_id'));

        if(!$product) {
            throw new \InvalidArgumentException('invalid product');
        }

        if(!$discount) {
            throw new \InvalidArgumentException('invalid discount');
        }

        $product = new Product($product);

        $product->setAttribute('discount_id', request('discount_id'));

        return $this->productRepo->store($product);
    }

    /**
     * @return mixed|null
     */
    public function detach()
    {
        $product = $this->productRepo->get(request('product_id'));
        $discount = $this->discountRepo->get(request('discount_id'));

        if(!$product) {
            throw new \InvalidArgumentException('invalid product');
        }

        if(!$discount) {
            throw new \InvalidArgumentException('invalid discount');
        }

        $product = new Product($product);

        $product->setAttribute('discount_id', null);

        return $this->productRepo->store($product);
    }
}
