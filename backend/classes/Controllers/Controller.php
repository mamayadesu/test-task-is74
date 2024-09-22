<?php

namespace test_is74\Controllers;

class Controller
{
    public static function runControllerHandler() : void
    {
        $controllerName = $_GET["c"] ?? "view";

        /** @var IController $controller */
        switch ($controllerName)
        {
            default:
            case "view":
                $controller = new View();
                $parameters = [
                    "view" => str_replace("/", "", $_GET["v"] ?? "index"),
                    "id" => $_GET["id"] ?? null
                ];
                break;

            case "rest":
                $controller = new Rest();
                $parameters = [
                    "action" => $_GET["a"] ?? ""
                ];
                break;
        }

        $controller->handleRequest($parameters);
    }
}