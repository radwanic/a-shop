<?php

namespace App\Foodics;

use App\Shop\Model;

/**
 * Class AbstractRepo
 * @package App\Foodics
 */
abstract class AbstractRepo
{
    /**
     * @param $array
     * @return mixed
     */
    public function toArray($array) {
        return json_decode(json_encode($array), true);
    }

    /**
     * @param Model $model
     */
    public function validate(Model $model) {
        foreach ($model->requiredAttributes() as $key) {
            if(!$model->getAttribute($key)) {
                throw new \InvalidArgumentException("$key is required");
            }
        }
    }

    /**
     * @param Model $model
     * @return mixed
     */
    abstract protected function store($model);

    /**
     * @param $id
     * @return mixed
     */
    abstract protected function get($id);

    /**
     * @return mixed
     */
    abstract protected function getAll();

    /**
     * @param $id
     * @return mixed
     */
    abstract public function delete($id);
}
