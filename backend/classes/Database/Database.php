<?php

namespace test_is74\Database;

use \Exception;

abstract class Database
{
    private static ?Database $instance = null;

    abstract protected function __construct(array $config);

    final public static function getInstance() : IDatabase
    {
        if (self::isDatabaseInitialized())
        {
            return self::$instance;
        }
        $config = require CONFIGS_DIR . "database.php";

        switch ($config["driver"])
        {
            case "sqlite3":
                self::$instance = new SQLite3Database($config);
                break;

            default:
                throw new Exception("Driver '" . $config["driver"] . "' is not supported");
        }

        return self::$instance;
    }

    public static function isDatabaseInitialized() : bool
    {
        return self::$instance !== null;
    }
}