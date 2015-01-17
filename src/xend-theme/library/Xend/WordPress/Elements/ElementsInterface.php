<?php

namespace Xend\WordPress\Elements;

interface ElementsInterface {
    
    /**
     * Register a Menu Location
     * @param string $location
     * @param string $description
     */
    public function registerMenuLocation($name, $description);
    
    /**
     * Register Multiple Menu Locations
     * @param array $menus Associative array in the format $location => $description
     */
    public function registerMenuLocations(array $locations);
    
    /**
     * Register a Sidebar
     * @param Sidebar $sidebar
     */
    public function registerSidebar(Sidebar $sidebar);
    
    /**
     * Register Widget 
     * 
     * @param Widget|string $widget Can be either a Xend\WordPress\Elements\Widget
     *        object or a the name of a class that extends \WP_Widget.
     */
    public function registerWidget($widget);
}
