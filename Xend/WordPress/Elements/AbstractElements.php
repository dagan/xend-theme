<?php

namespace Xend\WordPress\Elements;

use \Xend\WordPress\Exception;

abstract class AbstractElements implements ElementsInterface {
    
    /**
     * @var \Xend\WordPress\Events\EventsInterface
     */
    protected $_events;
    
    /**
     * @var \Zend_Controller_Front
     */
    protected $_frontController;
    
    protected $_sidebars;
    protected $_widgets;
    protected $_initWidgetsDone = false;
    protected $_controllerDirectories;
    protected $_wordSeparators;
    
    public function __construct(\Xend\WordPress\Events\EventsInterface $events) {
        $this->_frontController = $frontController;
        $this->_events = $events;
        $this->_events->addAction('widgets_init', array($this, 'initWidgets'));
        
        $this->_sidebars = array();
        $this->_widgets  = array();
    }
    
    public function initWidgets() {
        // Make sure the method isnt' being invoked directly
        if ($this->_initWidgetsDone) {
            throw new Exception("The initWidgets method has already been invoked. Do not invoke this method directly. It is "
                . "invoked by the WordPress widgets_init action.");
        }
        
        $this->_initWidgetsDone = true;
        
        // Register Sidebars
        foreach ($this->_sidebars as $sidebar) {
            /* @var $sidebar Sidebar */

            $args = array();
            
            if (isset($sidebar->id)) {
                $args['id'] = $sidebar->id;
            }
            
            if (isset($sidebar->name)) {
                $args['name'] = $sidebar->name;
            }
            
            if (isset($sidebar->description)) {
                $args['description'] = $sidebar->description;
            }
            
            if (isset($sidebar->class)) {
                $args['class'] = $sidebar->class;
            }
            
            if (isset($sidebar->beforeWidget)) {
                $args['before_widget'] = $sidebar->beforeWidget;
            }
            
            if (isset($sidebar->afterWidget)) {
                $args['after_widget'] = $sidebar->afterWidget;
            }
            
            if (isset($sidebar->beforeTitle)) {
                $args['before_title'] = $sidebar->beforeTitle;
            }
            
            if (isset($sidebar->aftertitle)) {
                $args['after_title'] = $sidebar->afterTitle;
            }
            
            $sidebar->id = register_sidebar($args);
        }
        
        // Register Widgets
        /**
         * @hack This bypasses $wp_widget_factory->register(), which is called by
         *       register_widget(). However, the only thing the register() method
         *       does is instantiate a new instance of the widget class and store
         *       it to the public $widgets property (an array) using the classname
         *       as the key. Since the $widgets array is public, this approach
         *       _should_ be acceptable, although I haven't found any specific
         *       documentation stating as much.
         */
        global $wp_widget_factory;
        if ($wp_widget_factory instanceof \WP_Widget_Factory) {
            foreach ($this->_widgets as $widget) {
                if ($widget instanceof \Xend\WordPress\Elements\Widget) {
                    if (!array_key_exists($widget->id_base, $wp_widget_factory)) {
                        $wp_widget_factory->widgets[$widget->id_base] = $widget;
                    }
                } else {
                    $wp_widget_factory->register($widget);
                }
            }
        } else {
            throw new Exception("Global WordPress \$wp_widget_factory is not defined");        
        }
    }

    public function registerMenuLocation($name, $description) {
        $this->registerMenuLocations(array($name => $description));
    }
    
    public function registerMenuLocations(array $locations) {
        if (!function_exists('register_nav_menus')) {
            throw new Exception("Native WordPress function wp_nav_menus() is not defined");
        }
    
        register_nav_menus($locations);
    }
    
    public function registerSidebar(Sidebar $sidebar) {
        if ($this->_initWidgetsDone == true) {
            throw new Exception('Cannot register a new Sidebar after the WordPress widgets_init action has occurred');
        }
        
        $this->_sidebars[] = $sidebar;
    }
    
    public function registerWidget($widget) {
        if ($this->_initWidgetsDone == true) {
            throw new Exception('Cannot register a new Widget after the WordPress widgets_init action has occurred');
        }
        
        $this->_widgets[] = $widget;
    }
}