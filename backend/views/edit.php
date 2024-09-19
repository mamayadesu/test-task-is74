<?php

use \test_is74\DTO\Tariff;
use \test_is74\Layout;
use \test_is74\Helpers\DateTimeHelper;

/** @var \test_is74\Controllers\View $this */

/** @var Tariff $model */
$model = Tariff::getNullObject();

$this->title = "Создание тарифа";

if ($this->id !== null)
{
    $this->title = "Редактирование тарифа";
    $model = Tariff::factory(intval($this->id));

    if (!$model->_loaded)
    {
        echo "Тариф не найден";
        return;
    }
}

Layout::getInstance()->addJsFile("TariffEditControl.js");

// "//<script>" чисто для PhpStorm, чтоб он понимал что это js
Layout::getInstance()->addJsCode("//<script>
(function() {
    new TariffEditControl('#tariff-edit', " . json_encode([
        'tariff_id' => $model->id
    ]) . ");
})();
");

?>

<div id="tariff-edit">
    <div class="form-block width-third">
        <label for="name" class="form-block__label">Название тарифа</label>
        <input type="text" id="name" maxlength="64" name="name" value="<?php echo htmlspecialchars($model->name); ?>" placeholder="Базовый">
    </div>

    <div class="form-block width-third">
        <label for="description" class="form-block__label">Описание</label>
        <textarea id="description" maxlength="1000" class="form-block__label" name="description"><?php echo htmlspecialchars($model->description); ?></textarea>
    </div>

    <div class="form-block width-third">
        <label for="speed" class="form-block__label">Скорость (Мбит/с)</label>
        <input type="number" id="speed" name="speed" value="<?php echo $model->speed; ?>" placeholder="200">
    </div>

    <div class="form-block width-third">
        <label for="price" class="form-block__label">Цена</label>
        <input type="text" id="price" name="price" value="<?php echo $model->price; ?>" placeholder="200">
    </div>

    <div class="form-block width-third">
        <label for="end" class="form-block__label">Дата окончания</label>
        <input type="text" id="end" name="end" value="<?php echo ($model->end ? DateTimeHelper::dateFromTimestamp($model->end) : ""); ?>" placeholder="дд.мм.гггг">
    </div>

    <div class="form-block width-third">
        <label for="image" class="form-block__label">Изображение</label>
        <input type="file" name="image" accept=".png, .jpg, .jpeg">
    </div>

    <button name="save" class="form-block__button">Сохранить</button>
</div>