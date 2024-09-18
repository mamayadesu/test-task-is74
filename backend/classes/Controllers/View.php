<?php

namespace test_is74\Controllers;

class View implements IController
{
    private string $title = "";
    private string $pageContent = "";

    public function handleRequest(array $parameters) : void
    {
        if (!preg_match("/[A-Za-z0-9]+/", $parameters["view"]))
        {
            http_response_code(404);
            $this->load("404");
            return;
        }

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

    public function load(string $viewName) : void
    {
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