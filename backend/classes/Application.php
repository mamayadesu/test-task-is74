<?php

namespace test_is74;

use test_is74\Database\Database;
use test_is74\Controllers\Controller;
use Throwable;

class Application
{
    public static function run() : void
    {
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
            @http_response_code(500);
            echo "<pre>";
            echo "Uncaught " . get_class($e) . " '" . $e->getMessage() . "' in " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
            echo "Stack Trace:\n";
            echo $e->getTraceAsString();
            echo "</pre>";
        }
    }
}