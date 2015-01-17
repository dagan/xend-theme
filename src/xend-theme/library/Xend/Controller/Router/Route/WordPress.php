<?php

namespace Xend\Controller\Router\Route;

/**
 * WordPress
 *
 * @author Dagan
 */
class WordPress implements \Zend_Controller_Router_Route_Interface
{
    public static function getInstance(\Zend_Config $config, \Zend_Controller_Dispatcher_Interface $dispatcher = null)
    {
        if (!isset($dispatcher))
                $dispatcher = $config->dispatcher;
        
        return new static($config, $dispatcher);
    }
    
    protected $_defaultModule;
    protected $_controllerClassPrefix;
    protected $_controllerDir;
    protected $_controllerCache = array();
    
    /**
     * @var \Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;
    
    public function __construct(\Zend_Controller_Dispatcher_Interface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
    }

    public function getVersion()
    {
        return 2;
    }

    public function assemble($data = array(), $reset = false, $encode = false)
    {
        // TODO Should utilize the defined WordPress permalink structures
    }

    public function match($request)
    {
        // Verify the request is a WordPress query
        if (!$request instanceof \Zend_Controller_Request_Http
            || !$request->getParam("wordpressQuery", false) instanceof \Xend\WOrdPress\Query\QueryInterface) {
                
            return false;
        }
        
        // Initialize routing parameters
        $this->_defaultModule         = $this->_dispatcher->getDefaultModule();
        $this->_controllerDir         = $this->_dispatcher->getControllerDirectory($this->_defaultModule);
        $this->_controllerClassPrefix = ($this->_dispatcher->getParam('prefixDefaultModule'))
        								 ? ucfirst($this->_defaultModule) . '_'
        								 : '';

        // Determine the hierarchy based on the request
        list($type, $subtype) = $request->getParam("wordpressQuery")->getQueryType(true);
        $hierarchy = array(array('index', 'index'));
        switch($type) {
            case 'error' : // WordPress 404
            	$hierarchy[] = array('index', 'error');
                $hierarchy[] = array('error', 'error');
                $hierarchy[] = array('error', 'error-' . $subtype);
                
            	// Add a ZF error handler for the error controller to recognize the 404
            	$error = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
            	$error->type = \Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE;
            	$error->exception = new \Zend_Controller_Router_Exception();
            	$request->setParam('error_handler', $error);
                break;
            case 'comment':
            	$hierarchy[] = array('index', 'comment');
                $hierarchy[] = array('comment', 'index');
                $hierarchy[] = array('comment', $subtype);
                break;
            case 'single':
            	$hierarchy[] = array('index', 'single');
                $hierarchy[] = array('index', $subtype);
                $hierarchy[] = array($subtype, 'index');
                $hierarchy[] = array($subtype, 'single');
                break;
            case 'archive':
                $hierarchy[] = array('index', 'archive');
                $hierarchy[] = array('index', $subtype . '-archive');
                $hierarchy[] = array($subtype, 'index');
                break;
            case 'category':
            	$hierarchy[] = array('index', 'category');
                $hierarchy[] = array('taxonomy', 'index');
                $hierarchy[] = array('taxonomy', 'category');
                $hierarchy[] = array('category', 'index');
                $hierarchy[] = array('category', $subtype);
                break;
            case 'tag':
            	$hierarchy[] = array('index', 'tag');
                $hierarchy[] = array('taxonomy', 'index');
                $hierarchy[] = array('taxonomy', 'tag');
                $hierarchy[] = array('tag', 'index');
                $hierarchy[] = array('tag', $subtype);
                break;
            case 'taxonomy':
            	$hierarchy[] = array('index', 'taxonomy');
                $hierarchy[] = array('index', $subtype);
                $hierarchy[] = array('taxonomy', 'index');
                $hierarchy[] = array('taxonomy', $subtype);
                break;
            case 'author':
            	$hierarchy[] = array('index', 'author');
                $hierarchy[] = array('author', 'index');
                $hierarchy[] = array('author', $subtype);
                break;
            case 'date':
            	$hierarchy[] = array('index', 'date');
                $hierarchy[] = array('date', 'index');
                $hierarchy[] = array('date', $subtype);
                break;
            case 'comment':
            	$hierarchy[] = array('index', 'comment');
            	$hierarchy[] = array('index', 'comment-' . $subtype);
                $hierarchy[] = array('comment', 'index');
                $hierarchy[] = array('comment', $subtype);
                break;
            case 'search':
            	$hierarchy[] = array('index', 'search');
                $hierarchy[] = array('search', 'index');
                $hierarchy[] = array('search', $subtype);
                $hierarchy[] = array($subtype, 'search');
                break;
            default:
            	$hierarchy[] = array('index', $type);
                $hierarchy[] = array($type, $subtype);
        }

        // Find the best dispatchable match in the child theme (if one exists)
        foreach (array_reverse($hierarchy) as $route) {
            
        	list($controller, $action) = $route;
            $controllerClass = $this->_getControllerClassName($controller);
            $actionName      = $this->_getActionName($action);
            
            if ($this->_isDispatchable($controllerClass, $actionName)) {
                return array('controller' => $controller, 'action' => $action);
            }
        }

        // Fallback to the Index controller and index action
        return array('module' => 'xend', 'controller' => 'index', 'action' => 'index');
    }

    protected function _getControllerClassName($controller)
    {
        return $this->_controllerClassPrefix . ucfirst($this->_formatName($controller)) . 'Controller';
    }

    protected function _getActionName($action)
    {
        return $this->_formatName($action) . 'Action';
    }

    protected function _formatName($name)
    {
    	// Decapitalized & replace non-word characters with undersocrds
    	$name = preg_replace('/\\W/', '', strtolower($name));
    	
    	// Capitalize letters following underscores and remove the preceding underscore
    	$name = preg_replace_callback('/_([a-z])/', function ($matches) { return strtoupper($matches[1]); }, $name);
    	
    	// Remove any remaining underscores
    	$name = str_replace('_', '', $name);
    	
    	return $name;
    }

    /**
     * Determines if the Given Controller Action is Dispatchable
     *
     * @param string $controllerClass
     * @param string $actionName
     * @return bool True if dispatchable. False if not.
     */
    protected function _isDispatchable($controllerClass, $actionMethod)
    {
        if (!array_key_exists($controllerClass, $this->_controllerCache)) {
        	
        	// Instantiate an empty array for the class name
        	$this->_controllerCache[$controllerClass] = array();
        	
            // Attempt to load the controller class
            $controllerFile = substr($controllerClass, strlen($this->_controllerClassPrefix)) . '.php';
            $controllerPath = $this->_controllerDir . '/' . $controllerFile;
            if (is_readable($controllerPath)) {
                include_once($controllerPath);
            } else {
                return false;
            }

            // Use reflection to confirm the class implements the Action Interface and retrieve an array of methods 
            if (class_exists($controllerClass)) {
                $reflection = new \ReflectionClass($controllerClass);
                $this->_controllerCache[$controllerClass] = array();
                if ($reflection->implementsInterface('Zend_Controller_Action_Interface')) {
	                foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
	                	$this->_controllerCache[$controllerClass][] = $method->name;
	                }
                }
            }
        }

        // If the action method exists, the route is dispatchable
		return in_array($actionMethod, $this->_controllerCache[$controllerClass]);
    }
}
