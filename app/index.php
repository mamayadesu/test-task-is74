<?php

define("ROOT_DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../");
define("APP_DIR", ROOT_DIR . "html/");
define("CONFIGS_DIR", ROOT_DIR . "backend/configs/");

require ROOT_DIR . "vendor/autoload.php";

use \test_is74\Controllers\Controller;
use \test_is74\Database\Database;

register_shutdown_function(function() : void {
    if (Database::isDatabaseInitialized())
    {
        Database::getInstance()->close();
    }
});

try
{
    Controller::runControllerHandler();
}
catch (Throwable $e)
{
    echo "<pre>";
    echo "Uncaught " . get_class($e) . " '" . $e->getMessage() . "' in " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
