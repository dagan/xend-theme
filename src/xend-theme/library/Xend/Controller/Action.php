<?php

namespace Xend\Controller;

/**
 * Action
 *
 * @author Dagan
 */
class Action extends \Zend_Controller_Action
{
    protected $_wordpress;
    
    /**
     * Retrieve the Xend\WordPress Instance
     * @throws \Xend\Exception
     * @return \Xend\WordPress
     */
    public function wordpress() {
        if ($this->_wordpress == null) {
            $this->_wordpress = $this->getInvokeArg('wordpress');
            if (!$this->_wordpress instanceof \Xend\WordPress) {
                throw new \Xend\Exception("A Xend\WordPress object was not set as a FrontController parameter");
            }
        }
        
        return $this->_wordpress;
    }
    
    public function init() {
        $this->initView();
        $this->view->assign('query', $this->wordpress()->query());
    }
}
