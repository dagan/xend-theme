<?php

/**
 * Xend Bootstrap
 *
 * @author Dagan
 */
class XendChild_Bootstrap extends \Zend_Application_Module_Bootstrap
{

    protected function _initWidgets() {
        $wordpress = $this->getApplication()->bootstrap('WordPress')->getResource('WordPress'); /* @var $wordpress \Xend\WordPress */
        
        require_once('controllers/WidgetController.php');
        $controller = new \WidgetController($wordpress, __DIR__ . '/views');
        $wordpress->elements()->registerWidget(new \Xend\WordPress\Elements\Widget('Sweet Tile', $controller));
    }
}
