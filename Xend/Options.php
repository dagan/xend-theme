<?php

namespace Xend;

class Options {
    
    protected $_childDirectory;
    protected $_options;
    
    public function __construct($childDirectory, array $options = array()) {
        $this->_childDirectory = $childDirectory;
        $this->setOptions($options);
    }
    
    public function setOptions(array $options) {
        $this->_options = array_merge($this->getDefaults(), $options);
    }
    
    public function getDefaults() {
        return array(
            'childModuleName'        => 'theme',
            'viewBasePath'           => false,
            'registerDefaultSidebar' => true,
            'registerDefaultMenu'    => true
        );
    }
    
    public function getChildDirectory() {
        return $this->_childDirectory;
    }
    
    public function getChildModuleName() {
        return $this->_options['childModuleName'];
    }
    
    public function setChildModuleName($name) {
        $this->_options['childModuleName'] = $name;
    }
    
    public function getViewBasePath() {
        return $this->_options['viewBasePath'];
    }
    
    public function setViewBasePath($viewBasePath) {
        $this->_options['viewBasePath'] = $viewBasePath;
    }
    
    public function registerDefaultSidebar($set = null) {
        if (isset($set)) {
            $this->_options['registerDefaultSidebar'] = $set;
        } else {
            return $this->_options['registerDefaultSidebar'];
        }
    }
    
    public function registerDefaultMenu($set = null) {
        if (isset($set)) {
            $this->_options['registerDefaultMenu'] = $set;
        } else {
            return $this->_options['registerDefaultMenu'];
        }
    }
}