<?php

namespace test_is74\Controllers;

class Rest implements IController
{
    private array $result = [];

    private function action_test() : void
    {
        $this->result = ["hello" => "world"];
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