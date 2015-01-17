<?php

namespace Xend\WordPress\ViewHelper;

use \Xend\WordPress\Exception;

abstract class AbstractViewHelper implements ViewHelperInterface {

    public function wordpress() {
        return $this;
    }

    public function getBlogInfo($info = 'name', $filter = 'raw') {
        if (!function_exists('get_bloginfo')) {
            throw new Exception('Native WordPress function get_bloginfo() is not defined');
        }

        return get_bloginfo($info, $filter);
    }
    
    public function blogInfo($info = 'name') {
        echo $this->getBlogInfo($info, 'display');
    }
    
    public function title($separator = '&raquo;', $return = false, $separatorLocation = '') {
        if (!function_exists('wp_title')) {
            throw new Exception('Native WordPress function wp_title is not defined');
        }
        
        if ($return) {
            return wp_title($separtor, false, $separatorLocation);
        } else {
            wp_title($separator, true, $separatorLocation);
        }
    }

    public function getXendDirectory() {
        if (!function_exists('get_template_directory')) {
            throw new Exception('Native WordPress function get_template_directory() is not defined');
        }
        
        return get_template_directory();
    }

    public function getXendUri() {
        if (!function_exists('get_template_directory_uri')) {
            throw new Exception('Native WordPress function get_template_directory_uri() is not defined');
        }
        
        return get_template_directory_uri();
    }

    public function getThemeDirectory() {
        if (!function_exists('get_stylesheet_directory')) {
            throw new Exception('Native WordPress function get_stylesheet_directory() is not defined');
        }
        
        return get_stylesheet_directory();
    }

    public function getThemeUri() {
        if (!function_exists('get_stylesheet_directory_uri')) {
            throw new Exception('Native WordPress function get_stylesheet_directory_uri() is not defined');
        }
        
        return get_stylesheet_directory_uri();
    }
    
    public function registerStyle($handle, $uri, $dependencies = array(), $version = false, $media = 'all') {
        if (!function_exists('wp_register_style')) {
            throw new Exception('Native WordPress function wp_register_style() is not defined');
        }
        
        wp_register_style($handle, $uri, $dependencies, $version, $media);
        return $this;
    }
    
    public function enqueueStyle($handle, $uri = '', $dependencies = array(), $version = false, $media = 'all') {
        if (!function_exists('wp_enqueue_style')) {
            throw new Exception('Native WordPress function wp_enqueue_style() is not defined');
        }
        
        wp_enqueue_style($handle, $src, $dependencies, $version, $media);
        return $this;
    }
    
    public function printStyles() {
        if (!function_exists('wp_print_styles')) {
            throw new Exception('Native WordPress function wp_print_styles() is not defined');
        }
        
    	wp_print_styles();
    }
    
    public function registerScript($handle, $uri, $dependencies = array(), $version = false, $inFooter = false) {
    	if (!function_exists('wp_register_script')) {
    	    throw new Exception('Native WordPress function wp_register_script is undefined');
    	}
    	
        wp_register_script($handle, $uri, $dependencies, $version, $inFooter);
    	return $this;
    }
    
	public function enqueueScript($handle, $uri = false, $dependencies = array(), $version = false, $inFooter = false) {
        if (!function_exists('wp_enqueue_script')) {
    	    throw new Exception('Native WordPress function wp_enqueue_script is undefined');
    	}
    	
        wp_enqueue_script($handle, $uri, $dependencies, $version, $inFooter);
    	return $this;
    }
    
    public function printScripts() {
        if (!function_exists('wp_print_scripts')) {
            throw new Exception('Native WordPress function wp_print_scripts is undefined');
        }
        
    	wp_print_scripts();
    }

    public function headAction() {
        if (!function_exists('wp_head')) {
            throw new Exception('Native WordPress function wp_head() is not defined');
        }

        return wp_head();
    }

    public function footerAction() {
        if (!function_exists('wp_footer')) {
            throw new Exception('Native WordPress function wp_footer() is not defined');
        }

        return wp_footer();
    }

    public function getBodyClass($class = null) {
        if (!function_exists('get_body_class')) {
            throw new Exception('Native WordPress function get_body_class() is not defined');
        }
        
        return implode(' ', get_body_class($class));
    }
    
    public function getPostClass(\Xend\WordPress\Posts\Post $post, $additionalClasses = '') {
        if (!function_exists('get_post_class')) {
            throw new Exception('Native WordPress function get_post_class() is not defined');
        }
        
        return implode(' ', get_post_class($additionalClasses, $post->id));
    }
    
    public function getCommentClass(\Xend\WordPress\Posts\Comment $comment, $additionalClasses = '') {
        if (!function_exists('get_comment_class')) {
            throw new Exception('Native WordPress function get_comment_class() is not defined');
        }
        
        return implode(' ', get_comment_class($additionalClasses, $comment->id));
    }
    
    public function renderMenu(Menu $menu, $return = false) {
        if (!function_exists('wp_nav_menu')) {
            throw new Exception("Native WordPress function wp_nav_menu() is not defined");    
        }
        
        $args = array();
        
        if ($menu->refBy == Menu::REF_BY_LOCATION) {
            $args['theme_location'] = $menu->ref;
        } else {
            $args['menu'] = $menu->ref;
        }
        
        if (isset($menu->container)) {
            $args['container'] = $menu->container;
            
            if (isset($menu->containerId)) {
                $args['container_id'] = $menu->containerId;
            }
            
            if (isset($menu->containerClass)) {
                $args['container_class'] = $menu->containerClass;
            }
        }
        
        if (isset($menu->menuId)) {
            $args['menu_id'] = $menu->menuId;
        }
        
        if (isset($menu->menuClass)) {
            $args['menu_class'] = $menu->menuClass;
        }
        
        if (isset($menu->renderPattern)) {
            $args['items_wrap'] = $menu->renderPattern;
        }
        
        if (isset($menu->beforeLink)) {
            $args['before'] = $optoins->beforeLink;
        }
        
        if (isset($menu->afterLink)) {
            $args['after'] = $optoins->afterLink;
        }
        
        if (isset($menu->beforeLinkText)) {
            $args['link_before'] = $menu->beforeLinkText;
        }
        
        if (isset($menu->afterLinkText)) {
            $args['link_after'] = $menu->afterLinkText;
        }
        
        if (isset($menu->depth)) {
            $args['depth'] = $menu->depth;
        }
        
        if ($return) {
            $args['echo'] = false;
            return wp_nav_menu($args);
        } else {
            wp_nav_menu($args);
        }
    }
    
    public function renderSidebar($ref, $return = false) {
        if (!function_exists('dynamic_sidebar')) {
            throw new Exception('Native WordPress function dynamic_sidebar is not defined');
        }
        
        if ($return) {
            ob_start();
            $result = dynamic_sidebar($ref);
            $output = ob_get_clean();
            return ($result) ? $output : false;
        } else {
            return dynamic_sidebar($ref);
        }
    }
}
