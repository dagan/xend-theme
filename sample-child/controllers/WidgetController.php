<?php

class WidgetController extends \Xend\WordPress\Elements\WidgetController {
    
    public function sweetTileWidgetAction() {
        $this->view->now = date(DateTime::RFC2822);
    }
    
    public function sweetTileFormAction() {
        $this->view->now = date(DateTime::RFC3339);
    }
} 