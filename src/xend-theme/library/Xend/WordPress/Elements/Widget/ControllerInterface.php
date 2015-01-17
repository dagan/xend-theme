<?php

namespace Xend\WordPress\Elements\Widget;

interface ControllerInterface {
    
    /**
     * Render a Widget Instance
     * 
     * @param \Xend\WordPress\Elements\Widget $widget The widget being rendered
     * @param array $instance The instance to render
     * @param array $args The render args (i.e. before_title, after_title, etc.)
     * @return void
     */
    public function renderWidget(\Xend\WordPress\Elements\Widget $widget, array $instance, array $ags);
    
    /**
     * Render a Widget Instance Admin Form
     * 
     * @param \Xend\WordPress\Elements\Widget $widget The widget being rendered
     * @param array $instance The instance to render
     * @return void
    */
    public function renderForm(\Xend\WordPress\Elements\Widget $widget, array $instance);
    
    /**
     * Filter an Update to a Widget Instance
     * 
     * @param \Xend\WordPress\Elements\Widget $widget The widget being updated
     * @param array $newInstance The new array settings submitted by the user
     * @param array $oldInstance The current array settings
     * @returns array|bool The filtered settings array or false to prevent update
     */
    public function filterUpdate(\Xend\WordPress\Elements\Widget $widget, array $newInstance, array $oldInstance);
}
