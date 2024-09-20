<?php

use \test_is74\Helpers\TariffHelper;
use \test_is74\Layout;

/** @var \test_is74\Controllers\View $this */

Layout::getInstance()->addCssFile("tariff_card.css");
Layout::getInstance()->addCssFile("index.css");
Layout::getInstance()->addJsFile("TariffsControl.js");

Layout::getInstance()->addJsCode("//<script>
(function() {
    new TariffsControl('#tariffs', " . json_encode([

    ]) . ")
})();
");

$this->title = "Просмотр тарифов";

$all_tariffs = TariffHelper::getAllTariffs();

?>

<div class="index__links">
    <a href="/edit">Создать тариф</a>
    <a href="/rest/pdf_import" target="_blank">Импорт в PDF</a>
</div>


<div class="tariffcard__wrapper" id="tariffs">
    <?php
    foreach ($all_tariffs as $tariff) {
        echo TariffHelper::getCompiledTariffCard($tariff);
    }
    ?>
</div>