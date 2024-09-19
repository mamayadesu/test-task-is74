<?php

namespace test_is74\Helpers;

use test_is74\Database\Database;
use test_is74\DTO\Tariff;

class TariffHelper
{
    private static ?string $template = null;

    public static function getCompiledTariffCard(Tariff $tariff) : string
    {
        if (self::$template === null)
        {
            self::$template = file_get_contents(ROOT_DIR . "backend/views/includes/tariff_card.php");
        }

        $result = self::$template;

        $properties = array_keys(get_class_vars(get_class($tariff)));

        foreach ($properties as $property)
        {
            if ($property == "_loaded")
            {
                continue;
            }

            $result = str_replace("{\$$property}", nl2br($tariff->$property), $result);
        }

        return $result;
    }

    /**
     * @return Tariff[]
     */
    public static function getAllTariffs(bool $onlyValid = true) : array
    {
        $sql = "SELECT * FROM tariffs";

        if ($onlyValid)
        {
            $sql .= " WHERE end > " . time();
        }

        $rows = Database::getInstance()->select($sql);

        $result = [];
        foreach ($rows as $row)
        {
            $result[] = Tariff::fromArray($row);
        }
        return $result;
    }
}