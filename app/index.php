<?php

define("ROOT_DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../");
define("APP_DIR", ROOT_DIR . "html/");
define("CONFIGS_DIR", ROOT_DIR . "backend/configs/");

require ROOT_DIR . "vendor/autoload.php";

use test_is74\Application;

Application::run();