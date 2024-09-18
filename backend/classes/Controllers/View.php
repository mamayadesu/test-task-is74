<?php

namespace test_is74\Controllers;

use test_is74\Layout;

class View implements IController
{
    private string $title = "";
    private string $pageContent = "";
    private ?string $id = null;

    public function handleRequest(array $parameters) : void
    {
        if (!preg_match("/[A-Za-z0-9]+/", $parameters["view"]))
        {
            http_response_code(404);
            $this->load("404");
            return;
        }

        $this->id = $parameters["id"];
        if (file_exists(ROOT_DIR . "backend/views/" . $parameters["view"] . ".php"))
        {
            $this->load($parameters["view"]);
        }
        else
        {
            http_response_code(404);
            $this->load("404");
        }
    }

    public function getHeaderTitle() : string
    {
        $config = require CONFIGS_DIR . "config.php";
        $websiteName = htmlspecialchars($config["websiteName"]);
        if ($this->title !== "")
        {
            return htmlspecialchars($this->title) . " | " . $websiteName;
        }

        return $websiteName;
    }

    public function includeStatic() : void
    {
        Layout::getInstance()->addCssFile("style.css");
    }

    public function load(string $viewName) : void
    {
        $this->includeStatic();
        ob_start();
        require ROOT_DIR . "backend/views/" . $viewName . ".php";
        $this->pageContent = ob_get_clean();

        if ($this->title === "")
        {
            $this->title = $viewName;
        }
        require ROOT_DIR . "backend/views/includes/template.php";
        exit;
    }
}