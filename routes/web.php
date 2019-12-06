<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $repo = new \App\Shop\Products\ProductRepo();
    $repo2 = new \App\Shop\Discounts\DiscountRepo();

    $mobile = new \App\Shop\Products\Product([
        'name' => 'mobile',
        'price' => 2.4,
    ]);

    $cover = new \App\Shop\Products\Product([
        'name' => 'cover',
        'price' => 0.6,
    ]);

//    $repo->store($mobile);
//    $repo->store($cover);

    $bundle = new \App\Shop\Products\Bundle([
        'name' => 'bundle',
        'price' => 2.8,
        'items' => [
            $mobile, $cover
        ]
    ]);

    $repo->store($bundle);

    $discount = new \App\Shop\Discounts\Discount([
        'name' => 'discount',
        'amount' => 0.5,
        'type' => 'FixedDiscount'
    ]);

    $repo2->store($discount);

});
