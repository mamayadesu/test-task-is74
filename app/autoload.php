<?php

define("ROOT_DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR);

spl_autoload_register(function($className)
{
    $root = "test_is74";

    if (substr($className, 0, strlen($root)) == $root)
    {
        $className = substr($className, strlen($root));
        $className = ROOT_DIR . "backend/classes" . $className;
    }

    $className = str_replace("\\", "/", $className) . ".php";
    if (file_exists($className))
    {
        require_once $className;
    }
});

use \test_is74\Database\Database;

register_shutdown_function(function() : void {
    if (Database::isDatabaseInitialized())
    {
        Database::getInstance()->close();
    }
});