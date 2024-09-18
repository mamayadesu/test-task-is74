<?php

namespace test_is74\DTO;

use test_is74\Database\Database;

class Tariff
{
    public string $name;
    public int $id, $speed, $created, $end, $updated;
    public float $price;

    public static function factory(int $id) : ?Tariff
    {
        $row = Database::getInstance()->selectOne("SELECT * FROM tariffs WHERE id=$id");
        if (count($row) == 0)
        {
            return null;
        }
        $obj = new Tariff();
        $obj->id = $row["id"];
        $obj->name = $row["name"];
        $obj->speed = $row["speed"];
        $obj->created = $row["created"];
        $obj->end = $row["end"];
        $obj->updated = $row["updated"];
        $obj->price = $row["price"];

        return $obj;
    }
}