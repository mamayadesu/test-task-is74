<?php

use \test_is74\Helpers\TariffHelper;

/** @var \test_is74\Controllers\View $this */

$this->title = "Просмотр тарифов";

$all_tariffs = TariffHelper::getAllTariffs();

?>

<a href="/edit">Создать тариф</a>

<div class="tariffcard__wrapper">
    <?php
    foreach ($all_tariffs as $tariff) {
        echo TariffHelper::getCompiledTariffCard($tariff);
    }
    ?>
</div>