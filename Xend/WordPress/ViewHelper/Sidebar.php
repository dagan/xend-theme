<?php

namespace Xend\WordPress;

use \Xend\Exception;

/**
 * Sidebar
 *
 * @author Dagan
 */
class Sidebar extends \Zend_View
{
    protected static $_sidebars;

    /**
     * Generates a New Sidebar and Returns a Reference to It
     *
     * The new sidebar will be registered with WordPress and can be
     * accessed later use the ::get() method.
     *
     * @param string $name
     * @param array  $sidebar_args
     * @param array  $view_config
     * @return \Xend\WordPress\Sidebar
     * @throws \Xend\Exception If the sidebar name is already in use
     * @see \Xend\WordPress\Sidebar::__construct()
     */
    public static function factory($name = 'sidebar', $args = array(), $view_config = array())
    {
        if (!is_string($name))
                throw new Exception('The first parameter passed to factory() must be a string');

        $key = strtolower($name);

        if (array_key_exists($key, self::$_sidebars))
                throw new Exception(sprintf('A sidebar named %s already exists', $name));

        $sidebar = new static($name, $args, $view_config);

        self::$_sidebars[$key] = $sidebar;

        return $sidebar;
    }

    /**
     * Retrieves a Sidebar From the Registry
     *
     * @param string $name
     * @return \Xend\WordPress\Sidebar
     * @throws Exception If the sidebar name is not a string
     */
    public static function get($name)
    {
        if (!is_string($name))
                throw new Exception('The first parameter passed to get() must be a string');

        $name = strtolower($name);

        return (array_key_exists($name, self::$_sidebars)) ? self::$_sidebars[$name] : false;
    }

    protected $_name;
    protected $_id;
    protected $_placeholder;
    protected $_script;
    protected $_render = true;
    protected $_widgets;

    /**
     * Constructs a New Sidebar Object and Register it With WordPress
     *
     * @param string $name        (optional) sidebar name
     * @param array  $args        (optional) sidebar arguments
     * <pre>
     *   string $description   A description of the sidebar to be shown in
     *                         the widgets panel
     *   string $before_widget HTML to include before each widget
     *   string $after_widget  HTML to include after each widget
     *   string $before_title  HTML to include before each widget title
     *   string $after_title   HTML to include after each widget title
     *   string $class         HTML class to assign to the widget object
     * </pre>
     * @param string $placeholder (optional) The content placeholder to assign the rendered sidebar to
     * @param array  $view_config (optional) \Zend_View config
     */
    public function __construct($name = 'sidebar', $args = array(), $placeholder = false, $view_config = array())
    {
        parent::__construct($view_config);

        // Borrow the ViewRenderer's inflector to get the base path and script name
        $view_renderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        /* @var $view_renderer \Zend_Controller_Action_Helper_ViewRenderer */
        $inflector = $view_renderer->getInflector();

        // Remember the current target so we can set it when we're done
        $_orig_target = $inflector->getTarget();

        $params = array(
            'moduleDir'  => $view_renderer->getModuleDirectory(),
            'module'     => $view_renderer->getModule(),
            'controller' => 'sidebars',
            'action'     => $name,
            'suffix'     => 'phtml');

        $inflector->setTarget($view_renderer->getViewBasePathSpec());
        $this->addBasePath($inflector->filter($params));


        $inflector->setTarget($view_renderer->getViewScriptPathSpec());
        $this->_script = $inflector->filter($params);

        if (isset($_orig_target))
                $inflector->setTarget($_orig_target);

        // Set the placeholder
        $this->setPlaceholder($placeholder);
        
        // Add the Xend View Helper path
        $this->addHelperPath(realpath(__DIR__ . '/../View/Helper'), '\Xend\View\Helper\\');

        // Register the sidebar with wordpress
        $args['name'] = $name;  // Don't allow the name to be overridden
        unset($args['id']);     // Don't allow the ID to be set

        $sidebar_args = array_merge(
                array(
                    'before_widget' => '<li class="widget">',
                    'after_widget'  => '</li>',
                    'before_title'  => '<h2 class="widget-title">',
                    'after_title'   => '</h1>'
                ),
                $args);

        $this->_id = \Xend\WordPress::registerDynamicSidebar($sidebar_args);
        $this->_name = $name;
    }
    
    /**
     * Sets the Layout Placeholder the Sidebar will be Rendered Into
     * @param string|bool $placeholder
     * @return \Xend\WordPress\Sidebar
     */
    public function setPlaceholder($placeholder)
    {
        $this->_placeholder = $placeholder;
        return $this;
    }
    
    /**
     * Retrieves the Layout Placeholder the Sidebar will be Rendered Into
     * 
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->_placeholder;
    }

    /**
     * Set the Sidebar Not to Render
     *
     * @param bool $do_not_render
     * @return \Xend\WordPress\Sidebar
     */
    public function setNoRender($do_not_render = true)
    {
        $this->_render = ($do_not_render) ? false : true;
        return $this;
    }
    

    /**
     * Renders the Sidebar
     *
     * @param array $vars   An optional array of variables to pass to the sidebar script
     * @param bool  $reset  Whether to clear any variables already set
     * @param bool  $return If false, a boolean value indicating whether the
     * output contained any widgets will be returned. If true, the rendered
     * string will be returned instead of output. (Default is false.)
     * @return bool|string
     */
    public function render($vars = array(), $reset = false, $return = false)
    {
        if (!$this->_render)
                return ($return) ? '' : false;

        // Optionally reset the arguments
        if ($reset)
                $this->clearVars();

        // Assign any passed variables
        $this->assign($vars);

        $ob = parent::render($this->_script);

        if ($return) {
            return $ob;
        } else {
            echo $ob;
            return $this->hasWidgets();
        }
    }

    /**
     * Retrieves the Rendered HTML Widgets
     *
     * @return string
     */
    public function getWidgets()
    {
        if (!isset($this->_widgets)) {
            ob_start();
            if ($this->wordpress()->getDynamicSidebar($this->_id)) {
                $this->_widgets = ob_get_clean();
            } else {
                $this->_widgets = false;
                ob_get_clean();
            }
        }

        return $this->_widgets;
    }

    /**
     * Checks Whether the Sidebar Has any Widgets
     *
     * @return bool
     */
    public function hasWidgets()
    {
        if (!isset($this->_widgets))
                $this->getWidgets();

        return (false === $this->_widgets) ? false : true;
    }
}
