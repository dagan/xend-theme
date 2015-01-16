<?php

namespace Xend\WordPress\Elements\Widget;

abstract class AbstractController implements ControllerInterface {
    
    protected $viewBasePath;
    protected $viewScriptSuffix;
    protected $viewScript;
    
    /**
     * @var \Zend_View_Abstract   
     */
    protected $view;
    
    /**
     * @var \Xend\WordPress
     */
    protected $wordpress;
    
    public function __construct(\Xend\WordPress $wordpress, $viewBasePath, $viewScriptSuffix = '.phtml') {
        $this->viewBasePath = $viewBasePath;
        $this->viewScriptSuffix = $viewScriptSuffix;
        $this->wordpress = $wordpress;
    }
    
    /**
     * Retrieve the Controller's View Object
     * 
     * @return Zend_View_Abstract
     */
    public function getView() {
        if (!isset($this->view)) {
            $this->view = new \Zend_View;
            $this->view->registerHelper($this->wordpress->viewHelper(), 'wordpress');
            $this->view->registerHelper(new \Xend\WordPress\Elements\Widget\ViewHelper($this->wordpress), 'widget');
            $this->view->setBasePath($this->viewBasePath);
        }
        
        return $this->view;
    }
    
    /**
     * Set the Controller's View Object
     * 
     * @param \Zend_View_Abstract $view
     */
    public function setView(\Zend_View_Abstract $view) {
        $this->view = $view;
    }
    
    public function renderWidget(\Xend\WordPress\Elements\Widget $widget, array $instance, array $args) {
        $action = str_replace(array(' ', '_', '.'), '-', strtolower($widget->name));
        $action = preg_replace('[^\w\d-]', '', $action);
        $action = strtolower($action) . '-widget';
        
        $this->initView($action, $widget, $instance, $args);
        echo $this->dispatch($action, $widget, array($instance, $args));
    }
    
    public function renderForm(\Xend\WordPress\Elements\Widget $widget, array $instance) {
        $action = str_replace(array(' ', '_', '.'), '-', strtolower($widget->name));
        $action = preg_replace('[^\w\d-]', '', $action);
        $action = strtolower($action) . '-form';
        
        $this->initView($action, $widget, $instance);
        echo $this->dispatch($action, $widget, array($instance));
    }
    
    public function filterUpdate(\Xend\WordPress\Elements\Widget $widget, array $newInstance, array $oldInstance) {
        return $newInstance;
    }
    
    /**
     * Handles Exceptions Thrown While Rendering Widgets
     * 
     * @param \Exception $ex
     * @param string $action
     * @param \Xend\WordPress\Elements\Widget $widget
     * @param array $args
     */
    public function errorHandler(\Exception $ex, $action, \Xend\WordPress\Elements\Widget $widget, array $args) {
       return sprintf('<p>An unexpected error occurred while rendering an instance of %s</p>', $widget->name); 
    }
    
    /**
     * Dispatch an Action and Render Its View Script
     * 
     * The action name will be converted to a method name such that
     * foo-bar will be converted to fooBarAction.
     * 
     * @param string $action The name of the action
     * @param \Xend\WordPress\Elements\Widget $widget
     * @param array $actionArgs An array of arguments to pass to the action
     */
    protected function dispatch($action, \Xend\WordPress\Elements\Widget $widget, array $args = array()) {
        
        // Determine the action method
        $method = '';
        foreach (explode('-', $action) as $segment) {
            $method .= ucfirst($segment);
        } 
        $method = lcfirst($method . 'Action'); 
        
        // Call the method if it exists (which is not required)
        if (method_exists($this, $method)) {
            call_user_func_array(array($this, $method), array_merge(array($widget), $args));
        }
        
        // Render the view script
        try {
            return $this->getView()->render($this->viewScript);
        } catch (\Exception $e) {
            return $this->errorHandler($e, $action, $widget, $args);
        }
    }
    
    /**
     * Initialize the View Object
     * 
     * @param  $actionName
     * @param \Xend\WordPress\Elements\Widget $widget
     * @param array $instance
     * @param array $args
     */
    protected function initView($action, \Xend\WordPress\Elements\Widget $widget, array $instance, array $args = array()) {
        
        // Initialize the View
        $view = $this->getView();
        $view->clearVars();
        $view->assign($args);
        
        // Base the script directory off of the controller class 
        $controllerName = get_class($this);
        $controllerName = str_replace(array('/', '_'), ' ', $controllerName);
        $controllerName = array_pop(explode(' ', $controllerName));
        
        // If the controller class ends in Controller remove it
        if (substr($controllerName, -10) == 'Controller') {
            $controllerName = substr($controllerName, 0, strlen($controllerName) - 10);
        }
        
        // Convert the controller to lowercase with hyphens separating words
        $controllerName = implode('-', preg_split('[A-Z]', $controllerName, PREG_SPLIT_NO_EMPTY));
        
        // Convert uppercase to lowercase
        $controllerName = strtolower($controllerName);
        
        // Base the view script on the controller name and action
        $this->viewScript = $controllerName . DIRECTORY_SEPARATOR . $action . $this->viewScriptSuffix;
        
        // If a widget view helper is available, call setWidget()
        $widgetHelper = $view->getHelper('widget');
        if ($widgetHelper instanceof \Xend\WordPress\Elements\Widget\ViewHelper) {
            $widgetHelper->init($widget, $instance, $args);
        }
    }
}
