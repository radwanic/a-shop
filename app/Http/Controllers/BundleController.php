<?php

namespace App\Http\Controllers;

use App\Shop\Products\Bundle;

/**
 * Class BundleController
 * @package App\Http\Controllers
 */
class BundleController extends ProductController
{
    /**
     * @return mixed|null
     */
    public function create()
    {
        $bundle = new Bundle(request()->only(['name', 'price', 'items']));

        return $this->repo->store($bundle);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed|null
     */
    public function update($id)
    {
        if (!$this->repo->get($id)) {
            return response(
                ['bundle' => 'not found'],
                404,
                ['Content-Type' => 'application/json']
            );
        }

        $data = request()->only(['name', 'price', 'items']);
        $data["id"] = $id;

        $bundle = new Bundle($data);

        return $this->repo->store($bundle);
    }

}
