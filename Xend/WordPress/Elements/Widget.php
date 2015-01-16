<?php

namespace Xend\WordPress\Elements;

class Widget extends \WP_Widget {
    
    /**
     * @var Widget\ControllerInterface
     */
    protected $_controller;
    
    /**
     * @param string $name The widget name that is shown to WordPress
     *        admin users
     * @param Widget\ControllerInterface $controller An instance of the
     *        widget's controller class. If a controller implements the
     *        Xend\WordPress\Elements\Widget\MultiControllerInterface,
     *        the same intance can be used to register each of the widgets
     *        it provides.
     */
    public function __construct($name, Widget\ControllerInterface $controller) {
        $idBase = get_class($controller) . '_' . str_replace(array('-', '.', ' '), '', $name);
        $idBase = preg_replace('/\\W/', '', $idBase);
        parent::__construct($idBase, $name);
        $this->_controller = $controller;
    }
    
    public function setWordPress(\Xend\WordPress $wordpress) {
        $this->_controller->setWordPress($wordpress);
    }

    /**
     * Render a Widget Instance
     * @see WP_Widget::widget()
     */
    public function widget($args, $instance) {
       $this->_controller->renderWidget($this, $instance, $args);
    }
    
    /**
     * Render an Admin Form for a Widget Intance
     * @see WP_Widget::form()
     */
    public function form($instance) {
       $this->_controller->renderForm($this, $instance);
    }
    
    /**
     * Filter a Widget Instance Update
     * @see WP_Widget::update()
     */
    public function update($newInstance, $oldInstance) {
        return $this->_controller->filterUpdate($this, $newInstance, $oldInstance);
    }
}
