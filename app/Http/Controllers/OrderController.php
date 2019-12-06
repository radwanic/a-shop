<?php

namespace App\Http\Controllers;

use App\Shop\Orders\Order;
use App\Shop\Orders\OrderRepo;
use App\Shop\Orders\OrderResource;

/**
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @var OrderRepo
     */
    protected $repo;
    /**
     * @var OrderResource
     */
    protected $resource;

    /**
     * OrderController constructor.
     * @param OrderRepo $orderRepo
     * @param OrderResource $orderResource
     */
    function __construct(OrderRepo $orderRepo, OrderResource $orderResource)
    {
        $this->repo = $orderRepo;
        $this->resource = $orderResource;
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
        $order = new Order(request()->only(['user_id', 'items']));

        return $this->repo->store($order);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed|null
     */
    public function update($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['order' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }
        $data = request()->only(['items']);
        $data["id"] = $id;

        $order = new Order($data);

        return $this->repo->store($order);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['order' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        return ['deleted' => $this->repo->delete($id)];
    }
}
