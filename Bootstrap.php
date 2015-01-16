<?php

use Xend\WordPress;
/**
 * Bootstrap
 *
 * @author Dagan
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{

    protected function _initChildTheme()
    {
        $fc = $this->bootstrap('FrontController')->getResource('FrontController');
        $fc->addControllerDirectory(__DIR__ . '/application/controllers', 'xend');

        if (function_exists('xend_child') && ($options = xend_child()) instanceof \Xend\Options) {
            /* @var $options \Xend\Options */
            
            // xend_theme_child_dir() is defined by the child theme and returns the path to the theme's root directory
            $dir = rtrim($options->getChildDirectory(), '/\\');
            if (!is_dir($dir)) {
                throw new Exception('Child theme definition must be a valid directory path');
            }

            // Add the child theme module
            $fc->addControllerDirectory($dir . '/controllers', $options->getChildModuleName());
            $fc->setDefaultModule($options->getChildModuleName());

            // Make sure modules are loaded
            $this->registerPluginResource('modules');
            $this->bootstrap('modules');

            return $options;
        } else {
            $fc->setDefaultModule('xend')
               ->setParam('prefixDefaultModule', true);
        }
    }
    
    protected function _initErrorHandler() {
        // Use xend over default module for errors
        $frontController = $this->bootstrap('FrontController')->getResource('FrontController'); /* @var $frontController \Zend_Controller_Front */
        $errorHandler = new Zend_Controller_Plugin_ErrorHandler();
        $errorHandler->setErrorHandlerModule('xend');
        $frontController->registerPlugin($errorHandler, 100);
        
        return $errorHandler;
    }
    
    protected function _initLayout() {
        
        $this->bootstrap(array('ChildTheme', 'WordPress'));
        $wordpress = $this->getResource('WordPress'); /* @var $wordpress \Xend\WordPress */
        $child     = $this->getResource('ChildTheme'); /* @var $child \Xend\Options */
        
        // Register Navigation Menu Locations
        if ($child->registerDefaultMenu()) {
            $wordpress->elements()->registerMenuLocation('xend_primary', 'Primary Navigation Menu');
        }
        
        // Register a Sidebar
        if ($child->registerDefaultSidebar()) {
            $wordpress->elements()->registerSidebar(new \Xend\WordPress\Elements\Sidebar('Primary Sidebar', 'This is the primary sidebar.'));
        }
        
        $viewHelper = $wordpress->viewHelper();
        
        // Register Stylesheets
        $viewHelper->registerStyle('bootstrap', 
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css');
        $viewHelper->registerStyle('bootstrap-theme', 
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css', array('bootstrap'));
        $viewHelper->registerStyle('xend',
            sprintf('%s/css/main.css', $viewHelper->getXendUri()), array('bootstrap', 'bootstrap-theme'));
        
        // Register Scripts
        $viewHelper->registerScript(
            'bootstrap',
            '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js',
            array('jquery'),
            '3.3.1',
            true); 
        

        $layout = \Zend_Layout::startMvc();
        $view = $layout->getView();
        
        // Set the Xend view directory explicitly, giving child themes a chance to override
        $view->addBasePath(realpath('./application/views'), 'Xend_');
        
        // Register the child viewBasePath
        $childBasePath = $child->getViewBasePath();
        if ($childBasePath !== false) {
            $view->addBasePath($childBasePath);
        }
        
        // Register the WordPress view helper
        if ($view instanceof \Zend_View_Abstract) {
            $view->registerHelper($viewHelper, 'wordpress');
        }
        
        return $layout;
    }

    protected function _initRequest()
    {
        $this->bootstrap(array('FrontController', 'WordPress'));
        
        $frontController = $this->getResource('FrontController');
        $wordPress = $this->getResource('WordPress');
        $request = new \Zend_Controller_Request_Http();
        
        $request->setParam('wordpressQuery', $wordPress->query());
        $frontController->setRequest($request);
    }

    protected function _initRouter()
    {
        $frontController = $this->bootstrap('FrontController')->getResource('FrontController');
        $dispatcher = $frontController->getDispatcher();
        $router = $frontController->getRouter();
        $router->addRoute('default', new \Xend\Controller\Router\Route\WordPress($dispatcher));
    }
    
    protected function _initWordPress()
    {
        $frontController = $this->bootstrap('FrontController')->getResource('FrontController');
        $wordpress = new \Xend\WordPress($frontController);
        $frontController->setParam('wordpress', $wordpress);
        
        return $wordpress;
    }
}
