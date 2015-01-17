<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../src/xend-theme/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Define WordPress constants
define('ABSPATH', realpath(__DIR__ . '/../library/wordpress') . '/');
define('WPINC', 'wp-includes');

// Instantiate the Zend Autoloader
set_include_path(realpath(__DIR__ . '/../src/xend-theme/library') . PATH_SEPARATOR . get_include_path());
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance()->registerNamespace('Xend', 'Xend');
