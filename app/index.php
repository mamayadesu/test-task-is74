<?php

define("ROOT_DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../");
define("APP_DIR", ROOT_DIR . "html/");
define("VIEWS_DIR", ROOT_DIR . "backend/views/");
define("CSV_SESSIONS_DIR", ROOT_DIR . "backend/csv_sessions/");
define("CONFIGS_DIR", ROOT_DIR . "backend/configs/");

require ROOT_DIR . "composer/vendor/autoload.php";

use test_is74\Application;

Application::run();