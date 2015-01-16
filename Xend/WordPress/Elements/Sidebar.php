<?php

namespace Xend\WordPress\Elements;

class Sidebar {
    
    protected $_id;
    protected $_name;
    protected $_description;
    protected $_class;
    protected $_beforeWidget;
    protected $_afterWidget;
    protected $_beforeTitle;
    protected $_afterTitle;
    
    public function __construct($name = null, $description = null) {
        if (isset($name)) {
            $this->name = $name;
        }
        
        if (isset($description)) {
            $this->description = $description;
        }
        
    }
    
    public function __get($property) {
        $getter = '_get' . ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }
    
    public function __set($property, $value) {
        $setter = '_set' . ucfirst($property);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }
    
    public function __isset($property) {
        return ($this->$property !== null);
    }
    
    public function _getId() {
        return $this->_id;
    }
    
    public function _setId($value) {
        $this->_id = $value;
    }
    
    public function _getName() {
        return $this->_name;
    }
    
    public function _setName($value) {
        $this->_name = $value;
    }
    
    public function _getDescription() {
        return $this->_description;
    }
    
    public function _setDescription($value) {
        $this->_description = $value;
    }
    
    public function _getClass() {
        return $this->_class;
    }
    
    public function _setClass($value) {
        $this->_class = $value;
    }
    
    public function _getBeforeWidget() {
        return $this->_beforeWidget;
    }
    
    public function _setBeforeWidget($value) {
        $this->_beforeWidget = $value;
    }
    
    public function _getAfterWidget() {
        return $this->_afterWidget;
    }
    
    public function _setAfterWidget($value) {
        $this->_afterWidget = $value;
    }
    
    public function _getBeforeTitle() {
        return $this->_beforeTitle;
    }
    
    public function _setBeforeTitle($value) {
        $this->_beforeTitle = $value;
    }
    
    public function _getAfterTitle() {
        return $this->_afterTitle;
    }
    
    public function _setAfterTitle($value) {
        $this->_afterTitle = $value;
    }
}