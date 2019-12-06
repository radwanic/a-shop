<?php

Route::get('/orders', 'OrderController@index');
Route::get('/orders/{id}', 'OrderController@show');
Route::post('/orders', 'OrderController@create');
Route::put('/orders/{id}', 'OrderController@update');
Route::delete('/orders/{id}', 'OrderController@delete');

Route::get('/discounts', 'DiscountController@index');
Route::get('/discounts/{id}', 'DiscountController@show');
Route::post('/discounts', 'DiscountController@create');
Route::put('/discounts/{id}', 'DiscountController@update');
Route::delete('/discounts/{id}', 'DiscountController@delete');

Route::get('/products', 'ProductController@index');
Route::get('/products/{id}', 'ProductController@show');
Route::post('/products', 'ProductController@create');
Route::put('/products/{id}', 'ProductController@update');
Route::delete('/products/{id}', 'ProductController@delete');

Route::post('/bundles', 'BundleController@create');
Route::put('/bundles/{id}', 'BundleController@update');

Route::put('/attach-discount', 'ProductDiscountController@attach');
Route::put('/detach-discount', 'ProductDiscountController@detach');


