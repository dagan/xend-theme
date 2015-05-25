<?php

/**
 * IndexController
 *
 * @author Dagan
 */
class XendTheme_IndexController extends \XendTheme\Controller\Action
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
