<?php

namespace App\Shop;

/**
 * Class Model
 * @package App\Shop
 */
abstract class Model
{
    /**
     * @var null
     */
    protected $id = null;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $acceptableAttributes = [];

    /**
     * @return array
     */
    abstract function requiredAttributes(): array;

    /**
     * @return array
     */
    public function acceptableAttributes()
    {
        return $this->acceptableAttributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return (array) $this->attributes;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * @param array $array
     */
    public function setAttributes(array $array)
    {
        foreach ($array as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->acceptableAttributes)) {
            $this->attributes[$key] = $value;

            return;
        }

        throw new \InvalidArgumentException('invalid attribute');
    }

    /**
     * @param $id
     */
    public function setID($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }
}