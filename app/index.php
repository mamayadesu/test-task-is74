<?php

require "autoload.php";

use \test_is74\Database\Database;

try
{
    $db = Database::getInstance();
}
catch (Throwable $e)
{
    echo "<pre>";
    echo "Uncaught " . get_class($e) . " '" . $e->getMessage() . "' in " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
}
