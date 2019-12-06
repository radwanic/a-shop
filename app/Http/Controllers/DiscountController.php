<?php

namespace App\Http\Controllers;

use App\Shop\Discounts\Discount;
use App\Shop\Discounts\DiscountRepo;
use App\Shop\Discounts\DiscountResource;

/**
 * Class DiscountController
 * @package App\Http\Controllers
 */
class DiscountController extends Controller
{
    /**
     * @var DiscountRepo
     */
    protected $repo;
    /**
     * @var DiscountResource
     */
    protected $resource;

    /**
     * DiscountController constructor.
     * @param DiscountRepo $discountRepo
     * @param DiscountResource $discountResource
     */
    function __construct(DiscountRepo $discountRepo, DiscountResource $discountResource)
    {
        $this->repo = $discountRepo;
        $this->resource = $discountResource;
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
        $discount = new Discount(request()->only(['name', 'amount', 'type']));

        return $this->repo->store($discount);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed|null
     */
    public function update($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['discount' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $data = request()->only(['name', 'amount', 'type']);
        $data["id"] = $id;

        $discount = new Discount($data);

        return $this->repo->store($discount);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['discount' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        return ['deleted' => $this->repo->delete($id)];
    }
}
