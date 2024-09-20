<?php

use \test_is74\DTO\CsvSession;

/** @var \test_is74\Controllers\View $this */

$this->title = "Импорт из CSV";

if ($this->id === null)
{
    require VIEWS_DIR . "includes/csv_import_file_upload.php";
}
else
{
    $id = intval($this->id);
    $session = CsvSession::factory($id);
    if (!$session->_loaded)
    {
        header("Location: /csv_import");
    }
    require VIEWS_DIR . "includes/csv_import_file_setup.php";
}