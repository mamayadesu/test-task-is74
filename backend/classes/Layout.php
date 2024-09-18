<?php

namespace test_is74;

class Layout
{
    private static ?Layout $instance = null;

    private array $addedCssFiles = [];
    private array $addedJsFiles = [];
    private string $addedJsCode = "";

    private function __construct()
    {

    }

    public static function getInstance() : Layout
    {
        if (self::$instance === null)
        {
            self::$instance = new Layout();
        }
        return self::$instance;
    }

    public function addCssFile(string $filename) : void
    {
        if (!in_array($filename, $this->addedCssFiles))
        {
            $this->addedCssFiles[] = $filename;
        }
    }

    public function addJsFile(string $filename) : void
    {
        if (!in_array($filename, $this->addedJsFiles))
        {
            $this->addedJsFiles[] = $filename;
        }
    }

    public function addJsCode(string $code) : void
    {
        $this->addedJsCode .= "\n$code";
    }

    public function compileFrontendHeaders() : string
    {
        $result = "";
        foreach ($this->addedCssFiles as $filename)
        {
            $frontend_path = "/static/css/$filename";
            $system_path = APP_DIR . "static/css/$filename";
            $result .= "<link rel='stylesheet' href='$frontend_path?v=" . filemtime($system_path) . "' />\n";
        }

        foreach ($this->addedJsFiles as $filename)
        {
            $frontend_path = "/static/js/$filename";
            $system_path = APP_DIR . "static/js/$filename";
            $result .= "<script src='$frontend_path?v=" . filemtime($system_path) . "'></script>\n";
        }

        $result .= "<script type='text/javascript'>window.onload = function() { " . $this->addedJsCode . "\n }</script>";

        return $result;
    }
}