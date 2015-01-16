<?php

namespace Xend\WordPress\Elements\Widget;

class ViewHelper extends \Zend_View_Helper_Abstract {

    /**
     * @var array
     */
    protected $_args;
    
    /**
     * @var array
     */
    protected $_instance;
    
    /**
     * @var \Xend\WordPress\Elements\Widget $widget
     */
    protected $_widget;
    
    /**
     * @var \Xend\WordPress
     */
    protected $_wordpress;

    public function __construct(\Xend\WordPress $wordpress) {
        $this->_wordpress = $wordpress;
    }
    
    public function widget() {
        return $this;
    }
    
    public function direct() {
        return $this;
    }
    
    /**
     * Set the Widget Object, Instance, and Args
     * 
     * @param \Xend\WordPress\Elements\Widget $widget
     * @param array $instance
     * @param array $args
     */
    public function init(\Xend\WordPress\Elements\Widget $widget, array $instance, $args = array()) {
        $this->_widget = $widget;
        $this->_instance = $instance;
        $this->_args = $args;
    }
    
    /**
     * Retrieve the Widget Title
     * @return string
     */
    public function getTitle() {
        return $this->getSetting('title');
    }
    
    /**
     * Print the Widget Title After Apply Filters
     * @return void
     */
    public function theTitle() {
        $title = $this->getTitle();
        echo $this->_wordpress->events()->applyFilters('widget_title', $title);
    }
    
    /**
     * Retrieve an Instance Setting
     * @param string $setting The setting to retrieve
     * @param mixed  $default A default value to return if the setting doesn't exist
     * @return mixed
     */
    public function getSetting($setting, $default = null) {
        return (isset($this->_instance) && array_key_exists($setting, $this->_instance))
                ? $this->_instance[$setting]
                : $default;
    }
    
    /**
     * Retrieve the 'before_widget' Value
     * @return string
     */
    public function getBeforeWidget() {
        return (isset($this->_args) && array_key_exists('before_widget', $this->_args))
                ? $this->_args['before_widget']
                : '';
    }
    
    /**
     * Print the 'before_widget' Value
     */
    public function beforeWidget() {
        echo $this->getBeforeWidget();
    }
    
    /**
     * Retreive the 'after_widget' Value
     * @return string
     */
    public function getAfterWidget() {
        return (isset($this->_args) && array_key_exists('after_widget', $this->_args))
                ? $this->_args['after_widget']
                : '';
    }
    
    /**
     * Print the 'after_widget' Value
     */
    public function afterWidget() {
        echo $this->getAfterWidget();
    }
    
    /**
     * Retrieve the 'before_title' Value
     * @return string
     */
    public function getBeforeTitle() {
        return (isset($this->_args) && array_key_exists('before_title', $this->_args))
                ? $this->_args['before_title']
                : '';
    }
    
    /**
     * Print the 'before_title' Value
     */
    public function beforeTitle() {
        echo $this->getBeforeTitle();
    }
    
    /**
     * Retrieve the 'after_title' Value
     */
    public function getAfterTitle() {
        return (isset($this->_args) && array_key_exists('after_title', $this->_args))
                ? $this->_args['after_title']
                : '';
    }
    
    /**
     * Print the 'after_title' Value
     */
    public function afterTitle() {
        echo $this->getAfterTitle();
    }
    
    /**
     * Retrieve the WordPress-Assigned ID for the Given Setting
     * @param  $setting
     * @return string
     */
    public function getFieldId($setting) {
        return $this->_widget->get_field_id($field_name);
    }
    
    /**
     * Print the WordPress-Assigned ID for the Given Setting
     * @param string $setting
     */
    public function fieldId($setting) {
        echo $this->getFieldId($setting);
    }
    
    /**
     * Retrieve the WordPress-Assigned Name for the Given Setting
     * @param string $setting
     * @return string
     */
    public function getFieldName($setting) {
        return $this->_widget->get_field_name($setting);
    }
    
    /**
     * Print the WordPress-Assigned Name for the Given Setting
     * @param unknown $setting
     */
    public function fieldName($setting) {
        echo $this->getFieldName($setting);
    }
}
