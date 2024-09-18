<?php

use \test_is74\DTO\Tariff;

/** @var \test_is74\Controllers\View $this */

/** @var Tariff $model */
$model = null;

$this->title = "Создание тарифа";

if ($this->id !== null)
{
    $this->title = "Редактирование тарифа";
    $model = Tariff::factory(intval($this->id));

    if ($model === null)
    {
        echo "Тариф не найден";
        return;
    }
}

?>


