<?php

namespace test_is74\DTO;

use test_is74\Database\Database;

class CsvSession extends DTO
{
    public ?string $filename;
    public ?int $id;

    public static function factory(?int $id) : ?CsvSession
    {
        $row = Database::getInstance()->selectOne("SELECT * FROM csv_sessions WHERE id=$id");
        if (count($row) == 0)
        {
            return static::getNullObject();
        }
        $obj = new CsvSession();
        $obj->id = $row["id"];
        $obj->filename = $row["filename"];
        $obj->_loaded = true;

        return $obj;
    }
}