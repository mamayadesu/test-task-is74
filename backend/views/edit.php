<?php

use \test_is74\DTO\Tariff;
use \test_is74\Layout;

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

Layout::getInstance()->addJsFile("TariffEditControl.js");

Layout::getInstance()->addJsCode("//<script>
(function() {
    
})();
");

?>

<div class="form-block width-third">
    <label for="name" class="form-block__label">Название тарифа</label>
    <input type="text" id="name" name="name" placeholder="Базовый">
</div>

<div class="form-block width-third">
    <label for="speed" class="form-block__label">Скорость (Мбит/с)</label>
    <input type="number" id="speed" name="speed" placeholder="200">
</div>

<div class="form-block width-third">
    <label for="price" class="form-block__label">Цена</label>
    <input type="text" id="price" name="price" placeholder="200">
</div>

<div class="form-block width-third">
    <label for="end" class="form-block__label">Дата окончания</label>
    <input type="text" id="end" name="end" placeholder="дд.мм.гггг">
</div>

<button name="save" class="form-block__button">Сохранить</button>