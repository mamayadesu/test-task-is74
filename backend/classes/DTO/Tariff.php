<?php

namespace test_is74\DTO;

use test_is74\Database\Database;

class Tariff extends DTO
{
    public ?string $name, $description;
    public ?int $id, $speed, $created, $end, $updated;
    public ?float $price;

    public static function factory(?int $id) : ?Tariff
    {
        $row = Database::getInstance()->selectOne("SELECT * FROM tariffs WHERE id=$id");
        if (count($row) == 0)
        {
            return static::getNullObject();
        }
        $obj = new Tariff();
        $obj->id = $row["id"];
        $obj->name = $row["name"];
        $obj->description = $row["description"];
        $obj->speed = $row["speed"];
        $obj->created = $row["created"];
        $obj->end = $row["end"];
        $obj->updated = $row["updated"];
        $obj->price = $row["price"];
        $obj->_loaded = true;

        return $obj;
    }

    public function getPriceAsString() : string
    {
        $value = round($this->price, 2);
        $price = explode(".", $value);
        if (count($price) == 1)
        {
            $value .= ".00";
        }
        else if (strlen($price[1]) == 1)
        {
            $value .= "0";
        }

        return (string) $value;
    }
}