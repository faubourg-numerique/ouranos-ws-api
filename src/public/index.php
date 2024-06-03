<?php

use Core\API;

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 0);

define("ROOT_DIRECTORY_PATH", dirname(realpath(__DIR__)));

require(implode(DIRECTORY_SEPARATOR, [ROOT_DIRECTORY_PATH, "vendor", "autoload.php"]));

require(implode(DIRECTORY_SEPARATOR, [ROOT_DIRECTORY_PATH, "Core", "Autoloader.php"]));

spl_autoload_register([Core\Autoloader::class, "autoload"]);

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIRECTORY_PATH);
$dotenv->load();
$dotenv->required("ENVIRONMENT")->allowedValues(["development", "production"]);
$dotenv->required("SHOW_ERRORS")->isBoolean();

define("ENVIRONMENT", $_ENV["ENVIRONMENT"]);
define("DEVELOPMENT", ENVIRONMENT === "development");
define("PRODUCTION", ENVIRONMENT === "production");

$enabledModules = [];
if (isset($_ENV["ENABLED_MODULES"])) {
    $temp = explode(",", $_ENV["ENABLED_MODULES"]);
    $temp = array_map("trim", $temp);
    $enabledModules = array_map("strtolower", $temp);
}

define("DATA_SERVICES_MODULE_ENABLED", in_array("data-services", $enabledModules));
define("WOT_MODULE_ENABLED", in_array("wot", $enabledModules));
define("DSC_MODULE_ENABLED", in_array("dsc", $enabledModules));

ini_set("display_errors", intval(filter_var($_ENV["SHOW_ERRORS"], FILTER_VALIDATE_BOOLEAN)));

date_default_timezone_set("UTC");

if (!DATA_SERVICES_MODULE_ENABLED || !WOT_MODULE_ENABLED || !DSC_MODULE_ENABLED) {
    trigger_error("At the moment, each of the \"data-services\", \"wot\" and \"dsc\" modules must be installed and enabled in order to ouranos-ws to work.", E_USER_ERROR);
    exit;
}

API::init();

API::router()->setNamespace("\\API");
require(implode(DIRECTORY_SEPARATOR, [ROOT_DIRECTORY_PATH, "routes", "api.php"]));

$pattern = implode(DIRECTORY_SEPARATOR, [ROOT_DIRECTORY_PATH, "init", "*.php"]);
$files = glob($pattern);

foreach ($files as $file) {
    require($file);
}

API::router()->run();
