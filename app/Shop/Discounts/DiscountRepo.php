<?php

namespace App\Shop\Discounts;

use App\Foodics\AbstractRepo;
use App\Shop\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class DiscountRepo
 * @package App\Shop\Discounts
 */
class DiscountRepo extends AbstractRepo
{
    /**
     * @param Discount $discount
     * @return mixed|null
     */
    public function store($discount)
    {
        if ($discount->getID()) {
            $this->update($discount);
        } else {
            $this->validate($discount);

            $this->insert($discount);
        }

        return $this->get($discount->getID());
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function get($id)
    {
        $record = DB::selectOne('SELECT * FROM discounts WHERE id = ?', [$id]);

        if(!$record) {
            return null;
        }

        return $this->toArray($record);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return DB::statement(
            "DELETE FROM discounts WHERE id = ?", [$id]
        );
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $results = DB::select(
            "SELECT * FROM discounts"
        );

        return $this->toArray($results);
    }

    /**
     * @param Discount $discount
     */
    private function insert($discount)
    {
        DB::statement(
            "INSERT INTO discounts (name, amount, type) VALUES (?, ?, ?)",
            [
                $discount->getAttribute('name'),
                $discount->getAttribute('amount'),
                $discount->getAttribute('type')
            ]
        );

        $discount->setID(DB::getPdo()->lastInsertId());
    }

    /**
     * @param Discount $discount
     */
    private function update($discount)
    {
        DB::statement(
            "UPDATE discounts SET name = ?, amount = ?, type = ? WHERE id = ?",
            [
                $discount->getAttribute('name'),
                $discount->getAttribute('amount'),
                $discount->getAttribute('type'),
                $discount->getID()
            ]
        );
    }

    /**
     * @param Model $discount
     */
    public function validate(Model $discount)
    {

        parent::validate($discount);

        $type = $discount->getAttribute('type');

        if(!class_exists("App\\Shop\\Discounts\\$type")) {
            throw new \InvalidArgumentException('invalid discount type');
        }
    }
}
