<?php

/**
 * IndexController
 *
 * @author Dagan
 */
class Xend_IndexController extends \Xend\Controller\Action
{    
    public function indexAction()
    {
        $this->view->assign('query', $this->wordpress()->query());
    }
    
    public function singleAction()
    {
        $this->view->assign('query', $this->wordpress()->query());
    }
}
