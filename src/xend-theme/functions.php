<?php

// Add library to the include path
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__  . '/library');

// Instantiate the Xend Loader
require_once('Xend/Loader.php');

// If the Zend Framework is not already on the class path, add it
if (!class_exists('Zend_Loader_Autoloader')) {
    require_once('Zend/Loader/Autoloader.php');
}
\Zend_Loader_Autoloader::getInstance();

// When Xend is loaded, set up the application
add_action('xend_loaded', function(Xend\WordPress $xend, $version) {

    // Define application environment
    defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    // Define application path
    defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', __DIR__ . '/application');


    // Define production options
    $options = array(
        'phpSettings' => array(
            'display_startup_errors' => false,
            'display_errors' => false,
        ),
        'bootstrap' => array(
            'path' => APPLICATION_PATH . '/../Bootstrap.php',
            'class' => 'Bootstrap',
        ),
        'appnamespace' => 'XendTheme',
        'resources' => array(
            'frontController' => array (
                'params' => array(
                    'displayExceptions' => false,
                )
            )
        ),
        'xend' => $xend,
        'xendVersion' => $version,
    );

    // Override some options for testing and development
    if (APPLICATION_ENV == 'testing') {
        $options['phpSettings']['display_startup_errors'] = true;
        $options['phpSettings']['display_errors'] = true;
    } elseif (APPLICATION_ENV == 'development') {
        $options['phpSettings']['display_startup_errors'] = true;
        $options['phpSettings']['display_errors'] = true;
        $options['resources']['frontController']['params']['displayExceptions'] = true;
    }

    // Bootstrap Xend Theme
    $xendTheme = new Zend_Application(APPLICATION_ENV, $options);
    $xendTheme->bootstrap();

    // Set up XendTheme to run with the xendtheme_run action
    add_action('xendtheme_run', array($xendTheme, 'run'), 10, 0);

}, 100, 2);

