<?php

namespace test_is74\Controllers;

use test_is74\Database\Database;
use test_is74\DTO\Tariff;
use test_is74\Helpers\DateTimeHelper;
use test_is74\Helpers\ImageHelper;

class Rest implements IController
{
    private array $result = [];

    private function action_test() : void
    {
        $this->result = ["hello" => "world"];
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

        if ($isNew)
        {
            $id = $db->insert("INSERT INTO tariffs (name, description, speed, price, end, created, updated) VALUES
        ('" . $fields["name"] . "', '" . $fields["description"] . "', " . $fields["speed"] . ", " . $fields["price"] . ", " . $fields["end"] . ", " . $fields["created"] . ", " . $fields["updated"] . ");");

            $this->result["success"] = true;
            $this->result["id"] = $id;
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
        }

        if (isset($_FILES["image"]))
        {
            $this->result["imagesaved"] = true;
            ImageHelper::saveUploadedImage((int)$id);
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