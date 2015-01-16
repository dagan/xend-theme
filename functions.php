<?php

// Instantiate the Composer autoloader
require_once('vendor/autoload.php');

chdir(__DIR__);

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define applicaiton path
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath('./application'));

// Bootstrap Xend Theme
global $xend_theme;
$xend_theme = new \Zend_Application(APPLICATION_ENV, __DIR__ . '/application/configs/application.ini');
$xend_theme->bootstrap();
