<?php

use Ubikz\SMS\Core\Application;

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dev');

define('ROOT_PATH', __DIR__.'/..');
define('WEB_PATH', ROOT_PATH.'/web');
define('APP_PATH', ROOT_PATH.'/app');
define('SOURCE_PATH', ROOT_PATH.'/src');
define('VENDOR_PATH', ROOT_PATH.'/vendor');
define('LOG_PATH', APP_PATH.'/logs');
define('CONF_PATH', APP_PATH.'/config');
define('MODULE_PATH', SOURCE_PATH.'/Module');

require VENDOR_PATH.'/autoload.php';

$app = new Application();
$app->run();
