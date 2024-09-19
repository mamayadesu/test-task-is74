<?php

use \test_is74\Database\Database;

/** @var \test_is74\Controllers\View $this */

$this->title = "table dump";

$rows = Database::getInstance()->select("SELECT * FROM tariffs");

echo "<pre>"; var_dump($rows); echo "</pre>";

phpinfo();