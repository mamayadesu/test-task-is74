<?php

use test_is74\Helpers\CsvSessionsHelper;

if (isset($_FILES["csv_file"]))
{
    if ($_FILES["csv_file"]["size"] == 0)
    {
        echo "<pre>Вы не выбрали файл</pre>";
    }
    else if ($_FILES["csv_file"]["type"] != "text/csv")
    {
        echo "<pre>Можно выбрать только CSV-файл</pre>";
    }
    else
    {
        $session_id = CsvSessionsHelper::createSession();
        header("Location: /csv_import/$session_id");
        exit;
    }
}

?>

<form method="post" enctype="multipart/form-data">
    <div class="form-block width-third">
        <label for="image" class="form-block__label">Выберите CSV-файл</label>
        <input required type="file" name="csv_file" accept=".csv">
    </div>

    <button type="submit" class="form-block__button" name="upload_csv">Продолжить</button>
</form>
