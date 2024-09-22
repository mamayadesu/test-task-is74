<?php

use \test_is74\Helpers\CsvSessionsHelper;
use \test_is74\Layout;

/** @var \test_is74\Controllers\View $this */
/** @var \test_is74\DTO\CsvSession $session */

$this->title .= " / " . $session->filename;

$filePath = CSV_SESSIONS_DIR . "session_" . $session->id . ".csv";
if (!file_exists($filePath))
{
    echo "Ошибка. Файл отсутствует на сервере. Загрузите файл ещё раз";
    return;
}

$data = CsvSessionsHelper::CsvToArray($filePath);

if (count($data) == 0)
{
    echo "Нельзя импортировать пустой CSV-файл";
    exit;
}

Layout::getInstance()->addJsFile("CsvImportControl.js");
Layout::getInstance()->addJsCode("//<script>

(function () {
    new CsvImportControl('#csv_import_setup', " . json_encode([
        "data" => $data
    ]) . ");
})();

");

?>

<div id="csv_import_setup">

    <div class="form-block width-full">
        <label class="form-block__label">Исходные данные</label>
        <table id="csv-table" border></table>
    </div>

    <div class="form-block width-full">
        <label class="form-block__label">Соотношение столбцов</label>

        <div class="form-block" style="margin-top: 0; margin-bottom: 0;">
            Название
            <select id="name_column" class="js-select-column"></select>
        </div>


        <div class="form-block" style="margin-top: 0; margin-bottom: 0;">
            Описание
            <select id="description_column" class="js-select-column"></select>
        </div>

        <div class="form-block" style="margin-top: 0; margin-bottom: 0;">
            Скорость
            <select id="speed_column" class="js-select-column"></select>
        </div>

        <div class="form-block" style="margin-top: 0; margin-bottom: 0;">
            Дата окончания
            <select id="end_column" class="js-select-column"></select>
        </div>

        <div class="form-block" style="margin-top: 0; margin-bottom: 0;">
            Цена
            <select id="price_column" class="js-select-column"></select>
        </div>
    </div>

    <div class="form-block width-full" style="display: flex;">
        <input type="checkbox" id="first_row_is_header" style="flex: 0">
        <label for="first_row_is_header" class="form-block__label" style="margin-bottom: 0">Первая строка - заголовок таблицы</label>
    </div>

    <div class="form-block width-full">
        <label class="form-block__label">Действие при совпадении названия тарифа</label>
        <select name="action_on_conflict">
            <option value="replace">Заменять всю запись</option>
            <option value="ignore">Игнорировать</option>
        </select>
    </div>

    <button type="submit" class="form-block__button" id="import_csv">Импортировать</button>
</div>