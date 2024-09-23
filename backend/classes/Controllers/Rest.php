<?php

namespace test_is74\Controllers;

use test_is74\Database\Database;
use test_is74\DTO\Tariff;
use test_is74\Helpers\DateTimeHelper;
use test_is74\Helpers\ImageHelper;
use test_is74\Helpers\TariffHelper;
use \mPDF;

class Rest implements IController
{
    const ALLOWED_IMAGE_TYPES = ["image/jpeg", "image/png"];

    const CSV_IMPORT_FIELD_NAMES = array(
        "name" => "Название",
        "description" => "Описание",
        "speed" => "Скорость",
        "price" => "Цена",
        "end" => "Дата окончания"
    );

    private array $result = [];

    private function action_csv_import() : void
    {
        $required_fields = ["name", "description", "speed", "price", "end"];

        $id = intval($_GET["id"] ?? null);
        if ($id === 0)
        {
            $this->result["error"] = "Session_id не указан";
            return;
        }

        $check = Database::getInstance()->selectOne("SELECT * FROM tariffs WHERE id=$id");
        if (count($check) == 0)
        {
            $this->result["error"] = "Ваша сессия истекла. Пожалуйста, попробуйте ещё раз";
            return;
        }

        $this->result["success"] = false;
        $data = @json_decode(file_get_contents("php://input"), true);
        if ($data === null)
        {
            $this->result["error"] = "Сервер получил некорректные данные";
            return;
        }
        $rows = $data["data"] ?? [];
        if (!is_array($rows) || count($rows) == 0)
        {
            $this->result["error"] = "Нет данных для импорта";
            return;
        }
        $tariffNameAppearances = array();
        $actionOnConflict = $data["actionOnConflict"] ?? "ignore";

        $line_number = 0;
        foreach ($rows as $index => $row)
        {
            $line_number++;

            // сначала проверим на наличие всех полей
            foreach ($required_fields as $required_field)
            {
                if (!isset($row[$required_field]))
                {
                    if (isset($row["name"]))
                    {
                        $error_msg = "Тариф '" . $row["name"] . "'";
                    }
                    else
                    {
                        $error_msg = "Строка $line_number";
                    }
                    $error_msg .= " не содержит поле '" . self::CSV_IMPORT_FIELD_NAMES[$required_field] . "'";
                    $this->result["error"] = $error_msg;
                    return;
                }
            }

            // отдельно проверим на их заполненность
            foreach ($required_fields as $required_field)
            {
                if ($row[$required_field] == "")
                {
                    if ($row["name"] != "")
                    {
                        $this->result["error"] = "Тариф '" . $row["name"] . "', поле '" . self::CSV_IMPORT_FIELD_NAMES[$required_field] . "' пустое";
                    }
                    else
                    {
                        $this->result["error"] = "Строка $line_number, поле '" . self::CSV_IMPORT_FIELD_NAMES[$required_field] . "' пустое";
                    }
                }
            }

            if (!isset($tariffNameAppearances[$row["name"]]))
            {
                $tariffNameAppearances[$row["name"]] = [];
            }

            $tariffNameAppearances[$row["name"]][] = $line_number;

            if (!DateTimeHelper::validateDate($row["end"]))
            {
                $this->result["error"] = "Тариф '" . $row["name"] . "', дата окончания некорректна";
                return;
            }

            $rows[$index]["end"] = DateTimeHelper::toTimestamp($row["end"]);

            if (!preg_match("/^[1-9][0-9\.]{0,15}$/", $row["price"]))
            {
                $this->result["error"] = "Тариф '" . $row["name"] . "', цена может быть только целым или дробным числом";
                return;
            }

            $rows[$index]["price"] = round($row["price"], 2);

            if (!preg_match("/^[1-9][0-9]{0,15}$/", $row["speed"]))
            {
                $this->result["error"] = "Тариф '" . $row["name"] . "', скорость может быть только целым числом";
                return;
            }

            $rows[$index]["speed"] = intval($row["speed"]);
        }

        foreach ($tariffNameAppearances as $name => $linesList)
        {
            if (count($linesList) > 1)
            {
                $this->result["error"] = "Тариф '" . $name . "', повторение на следующих строках: " . implode(", ", $linesList);
                return;
            }
        }
        $inserts = [];

        $this->result["rows"] = $rows;

        // после всех валидаций приступаем наконец к заполнению данных
        foreach ($rows as $row)
        {
            $name = Database::getInstance()->escapeString($row["name"]);
            $description = Database::getInstance()->escapeString($row["description"]);
            $speed = $row["speed"];
            $price = $row["price"];
            $end = $row["end"];

            $check = Database::getInstance()->selectOne("SELECT * FROM tariffs WHERE name='$name'");
            if (count($check) > 0)
            {
                if ($actionOnConflict == "replace")
                {
                    Database::getInstance()->execute("UPDATE tariffs SET 
                    description='$description',
                    speed=$speed,
                    price=$price,
                    end='$end',
                    updated=" . time() . "
                    WHERE name='$name'");
                }
            }
            else
            {
                $inserts[] = "('$name', '$description', $speed, $price, '$end', " . time() . ", " . time() . ")";
            }
        }

        if (count($inserts) > 0)
        {
            $insert_sql = "INSERT INTO tariffs (name, description, speed, price, end, created, updated) VALUES " . implode(", ", $inserts) . ";";
            Database::getInstance()->execute($insert_sql);
        }

        Database::getInstance()->execute("DELETE FROM csv_sessions WHERE id=$id");

        if (file_exists(CSV_SESSIONS_DIR . "session_$id.csv"))
        {
            unlink(CSV_SESSIONS_DIR . "session_$id.csv");
        }

        $this->result["success"] = true;
    }

    private function action_csv_export() : void
    {
        $tariffs = TariffHelper::getAllTariffs();
        $rows = [
            [
                "Название",
                "Описание",
                "Скорость",
                "Дата окончания",
                "Цена"
            ]
        ];

        foreach ($tariffs as $tariff)
        {
            $rows[] = [
                $tariff->name,
                $tariff->description,
                $tariff->speed,
                DateTimeHelper::dateFromTimestamp($tariff->end),
                $tariff->price
            ];
        }
        $stdout = fopen("php://output", "w");
        header("Content-Type: octet/stream");
        header("Content-Disposition: attachment; filename=\"tariffs.csv\"");
        foreach ($rows as $row)
        {
            fputcsv($stdout, $row);
        }
        exit;
    }

    private function action_pdf_export() : void
    {
        $html = "<style>\n" . file_get_contents(APP_DIR . "static/css/tariff_card.css") . "\n</style>\n";

        $all_tariffs = TariffHelper::getAllTariffs();
        $html .= "<div class='tariffcard__wrapper'>";
        $version_src = "";
        foreach ($all_tariffs as $tariff)
        {
            $version_src .= $tariff->updated;
            $html .= TariffHelper::getCompiledTariffCard($tariff);
        }
        $version = md5($version_src);

        $db_version = Database::getInstance()->selectOne("SELECT * FROM options WHERE name='pdf_file_version'")["value"];

        // проверяем кэшированную версию pdf-файла
        if ($version == $db_version && $_GET["id"] != "force")
        {
            header("Location: /tariffs.pdf?v=$version");
            exit;
        }

        if (file_exists(APP_DIR . "tariffs.pdf"))
        {
            unlink(APP_DIR . "tariffs.pdf");
        }

        Database::getInstance()->execute("UPDATE options SET value='$version' WHERE name='pdf_file_version'");

        $html .= "</div>";
        $pdf = @new mPDF();

        // отключаем вывод
        ob_start();
        @$pdf->WriteHTML($html);

        // кэшируем файл для оптимизации
        @$pdf->Output("tariffs.pdf");
        ob_get_clean();

        header("Location: /tariffs.pdf?v=$version");
        exit;
    }

    private function action_delete_tariff() : void
    {
        $id = intval($_GET["id"] ?? 0);
        $this->result["success"] = false;
        if ($id === 0)
        {
            $this->result["error"] = "Тариф не указан";
            return;
        }

        Database::getInstance()->execute("DELETE FROM tariffs WHERE id=$id");

        $filePath = APP_DIR . "static/upload/tariff_$id.jpg";
        if (file_exists(APP_DIR . "static/upload/tariff_$id.jpg"))
        {
            unlink($filePath);
        }

        $this->result["success"] = true;
    }

    private function action_save_tariff() : void
    {
        $id = intval($_GET["id"]);
        $isNew = !$id;
        $this->result["success"] = false;
        $fields = [
            "name" => $_POST["name"] ?? "",
            "description" => $_POST["description"] ?? "",
            "speed" => $_POST["speed"] ?? "",
            "price" => $_POST["price"] ?? "",
            "end" => $_POST["end"] ?? ""
        ];

        foreach ($fields as $fieldName => $value)
        {
            if (!$value)
            {
                $this->result["error"] = "Не все поля заполнены";
                return;
            }
        }

        if (!DateTimeHelper::validateDate($fields["end"]))
        {
            $this->result["error"] = "Дата окончания указана неверно";
            return;
        }

        if (strlen($fields["name"]) > 64)
        {
            $this->result["error"] = "Название слишком длинное";
            return;
        }

        if (strlen($fields["description"]) > 1000)
        {
            $this->result["error"] = "Описание слишком длинное";
            return;
        }

        if (!isset($_FILES["image"]) && ($isNew || !file_exists(APP_DIR . "static/upload/tariff_$id.jpg")))
        {
            $this->result["error"] = "Изображение не загружено";
            return;
        }

        $db = Database::getInstance();

        $fields["name"] = $db->escapeString($fields["name"]);
        $fields["description"] = $db->escapeString($fields["description"]);
        $fields["speed"] = intval($fields["speed"]);
        $fields["price"] = round($fields["price"], 2);
        $fields["end"] = DateTimeHelper::toTimestamp($fields["end"]);
        $fields["updated"] = time();
        if ($isNew)
        {
            $fields["created"] = $fields["updated"];
        }

        $check = Database::getInstance()->selectOne("SELECT * FROM tariffs WHERE name='" . Database::getInstance()->escapeString($fields["name"]) . "'");

        if (count($check) > 0)
        {
            $tariff = Tariff::fromArray($check);

            if ($tariff->id != $id)
            {
                $this->result["error"] = "Тариф с таким названием уже есть";
                return;
            }
        }

        $tempId = null;
        if (isset($_FILES["image"]))
        {
            if (!in_array(mime_content_type($_FILES["image"]["tmp_name"]), self::ALLOWED_IMAGE_TYPES)) {
                $this->result["error"] = "Можно загрузить изображения только .png и .jpg/.jpeg формата";
                unlink($_FILES["image"]["tmp_name"]);
                return;
            }

            if ($isNew)
            {
                $tempId = md5(microtime(true) . "-" . rand(1000, 10000));
                ImageHelper::saveUploadedImage($tempId);
            }
            else
            {
                ImageHelper::saveUploadedImage($id);
            }

        }

        if ($isNew)
        {
            $id = $db->insert("INSERT INTO tariffs (name, description, speed, price, end, created, updated) VALUES
        ('" . $fields["name"] . "', '" . $fields["description"] . "', " . $fields["speed"] . ", " . $fields["price"] . ", " . $fields["end"] . ", " . $fields["created"] . ", " . $fields["updated"] . ");");

            $this->result["success"] = true;
            $this->result["id"] = $id;
            if ($tempId !== null)
            {
                rename(APP_DIR . "static/upload/tariff_" . $tempId . ".jpg", APP_DIR . "static/upload/tariff_" . $id . ".jpg");
            }
        }
        else
        {
            $db->execute("UPDATE tariffs SET
            name='" . $fields["name"] . "',
            description='" . $fields["description"] . "',
            speed=" . $fields["speed"] . ",
            price=" . $fields["price"] . ",
            end=" . $fields["end"] . ",
            updated=" . $fields["updated"] . "
            
            WHERE id=$id;");
            $this->result["success"] = true;
            $this->result["id"] = $id;
        }
    }

    public function handleRequest(array $parameters) : void
    {
        $action = $_GET["a"];

        if ($action === "")
        {
            http_response_code(404);
            $this->result = [
                "error" => "Invalid URL"
            ];
        }
        else
        {
            $method_name = "action_" . $action;
            if (method_exists($this, $method_name))
            {
                $this->$method_name();
            }
            else
            {
                http_response_code(404);
                $this->result = [
                    "error" => "Action not found"
                ];
            }
        }

        die(json_encode($this->result));
    }
}