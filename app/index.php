<?php

define("TEST_IS74", true);

require "autoload.php";

use \test_is74\Controllers\Controller;

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
