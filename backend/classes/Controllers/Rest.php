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

    private array $result = [];

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
            header("Location: /tariffs.pdf");
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
        @$pdf->Output("tariffs.pdf");
        ob_get_clean();

        header("Location: /tariffs.pdf");
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
        $id = $_GET["id"];
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