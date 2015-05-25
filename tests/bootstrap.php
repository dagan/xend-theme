<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../src/xend-theme/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Instantiate the Zend, Xend, and XendTheme autoloaders
set_include_path(get_include_path()
    . PATH_SEPARATOR . realpath(__DIR__ . '/../src/xend-theme/library')
    . PATH_SEPARATOR . realpath(__DIR__ . '/../vendor/zendframework/zendframework1/library/')
);
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance()->registerNamespace('XendTheme');
require_once(__DIR__ . '/../vendor/xend/xend/src/Autoloader.php');
\Xend\Autoloader::init();
