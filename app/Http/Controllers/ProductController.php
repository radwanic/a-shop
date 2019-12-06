<?php

namespace App\Http\Controllers;

use App\Shop\Products\Product;
use App\Shop\Products\ProductResource;
use App\Shop\Products\ProductRepo;

/**
 * Class ProductController
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * @var ProductRepo
     */
    protected $repo;
    /**
     * @var ProductResource
     */
    protected $resource;

    /**
     * ProductController constructor.
     * @param ProductRepo $productRepo
     * @param ProductResource $productResource
     */
    function __construct(ProductRepo $productRepo, ProductResource $productResource)
    {
        $this->repo = $productRepo;
        $this->resource = $productResource;
    }

    /**
     * @return array
     */
    public function index()
    {
        return $this->resource->collection(
            $this->repo->getAll()
        );
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return $this->resource->make(
            $this->repo->get($id)
        );
    }

    /**
     * @return mixed|null
     */
    public function create()
    {
        $product = new Product(request()->only(['name', 'price', 'bundle_id']));

        return $this->repo->store($product);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed|null
     */
    public function update($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['product' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $data = request()->only(['name', 'price', 'bundle_id']);
        $data["id"] = $id;

        $product = new Product($data);

        return $this->repo->store($product);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['product' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        return ['deleted' => $this->repo->delete($id)];
    }
}
