<?php

use Ubikz\SMS\Application;

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : "dev");

define('ROOT_PATH', __DIR__ . '/..');
define('APP_PATH', ROOT_PATH . '/app');
define('WEB_PATH', ROOT_PATH . '/web');
define('SOURCE_PATH', ROOT_PATH . '/src');
define('VENDOR_PATH', ROOT_PATH . '/vendor');

define('DATA_PATH', APP_PATH . '/data');
define('CONF_PATH', APP_PATH . '/config');
define('ROUTE_PATH', APP_PATH . '/route');

define('CACHE_PATH', DATA_PATH . '/cache');
define('FIXTURE_PATH', DATA_PATH . '/fixtures');
define('LOG_PATH', DATA_PATH . '/logs');

if (file_exists($path = VENDOR_PATH . '/autoload.php')) {
    require $path;
}

$app = new Application();
$app->run();
