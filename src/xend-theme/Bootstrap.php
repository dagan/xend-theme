<?php

use Xend\WordPress;
/**
 * Bootstrap
 *
 * @author Dagan
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader() {
        \Zend_Loader_Autoloader::getInstance()->registerNamespace('XendTheme');
    }

    protected function _initCommentForms() {
        $wordpress = $this->bootstrap(array('Autoloader', 'WordPress'))->getResource('WordPress');

        if (get_option("require_name_email")) {
            $requiredLabel = '<span class="required">*</span>';
            $requiredAria = ' aria-required="true" ';
        } else {
            $requiredLabel = '';
            $requiredAria = '';
        }

        $commentForm = new \Xend\WordPress\ViewHelper\CommentForm();
        $commentForm->format = 'html5';
        $commentForm->fields = array(
                'author' => '<div class="comment-form-author form-group">' .
                                '<label for="author">' . __('Name') . $requiredLabel . '</label>' .
                                '<input id="author" class="form-control" name="author" type="text"' . $requiredAria . '/>' .
                            '</div>',
                'email'  => '<div class="comment-form-email form-group">' .
                                '<label for="email">' . __('Email') . $requiredLabel . '</label>' .
                                '<input id="email" class="form-control" name="email" type="email" aria-describedby="email-notes"' . $requiredAria . '/>' .
                            '</div>',
                'url' => '<div class="comment-form-url form-group">' .
                             '<label for="url">' . __('Website') . '</label>' .
                             '<input id="url" class="form-control" name="url" type="url"/>' .
                         '</div>',
        );
        $commentForm->commentField = '<div class="comment-form-comment form-group">' .
                                         '<label for="comment">' . _x('Comment', 'noun') . '</label>' .
                                         '<textarea id="comment" class="form-control" name="comment" rows="6" aria-describedby="form-allowed-tags" aria-required="true"></textarea>' .
                                     '</div>';
        // $commentForm->afterMessage = '<p class="form-allowed-tags help-block col-sm-offset-2" id="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>';
        $commentForm->submitClass = 'btn btn-primary';

        $wordpress->viewHelper()->registerCommentForm($commentForm, 'xend-theme');
        $wordpress->viewHelper()->setDefaultCommentForm('xend-theme');
    }

    protected function _initCommentLists() {
        $wordpress = $this->bootstrap(array('Autoloader', 'WordPress'))->getResource('WordPress');
        $commentList = new \Xend\WordPress\ViewHelper\CommentList(array('format' => 'html5', 'style' => 'div'));
        $wordpress->viewHelper()->registerCommentList($commentList, 'xend-theme');
        $wordpress->viewHelper()->setDefaultCommentList('xend-theme');
    }
    
    protected function _initErrorHandler() {
        // Explicitly use xend-theme for error handling
        $frontController = $this->bootstrap('FrontController')->getResource('FrontController');
        $errorHandler = new Zend_Controller_Plugin_ErrorHandler();
        $errorHandler->setErrorHandlerModule('xend-theme');
        $frontController->registerPlugin($errorHandler, 100);
        
        return $errorHandler;
    }
    
    protected function _initLayout()
    {
        $this->bootstrap(array('Autoloader', 'Theme', 'WordPress'));
        $wordpress = $this->getResource('WordPress'); /* @var $wordpress \Xend\WordPress */
        $theme = $this->getResource('Theme'); /* @var $theme \XendTheme\Options */

        // Register Navigation Menu Locations
        if ($theme->registerDefaultMenu()) {
            $wordpress->elements()->registerMenuLocation('xend_primary', 'Primary Navigation Menu');
        }

        // Register a Sidebar
        if ($theme->registerDefaultSidebar()) {
            $defaultSidebar = new \Xend\WordPress\Elements\Sidebar('Primary Sidebar', 'This is the primary sidebar.');
            $wordpress->elements()->registerSidebar($defaultSidebar);
        }

        $viewHelper = $wordpress->viewHelper();

        // Register Stylesheets
        $viewHelper->registerStyle('bootstrap-theme', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css');
        $viewHelper->registerStyle('xend-theme', $viewHelper->getThemeUri()  . '/css/main.css', array('bootstrap-theme'));

        // Register Scripts
        $viewHelper->registerScript('bootstrap', $viewHelper->getThemeUri() . '/js/bootstrap.min.js', array('jquery'), '3.3.1', true);


        $layout = \Zend_Layout::startMvc();
        $view = $layout->getView();

        // Set the Xend view directory explicitly, giving child themes a chance to override
        $view->addBasePath(realpath('./application/views'), 'XendTheme_');

        // Register the child viewBasePath
        $childBasePath = $theme->getViewBasePath();
        if ($childBasePath !== false) {
            $view->addBasePath($childBasePath);
        }
        
        // Register the WordPress view helper
        if ($view instanceof \Zend_View_Abstract) {
            $view->registerHelper($viewHelper, 'wordpress');
        }
        
        return $layout;
    }

    protected function _initRequest()
    {
        $this->bootstrap(array('FrontController', 'WordPress'));
        
        $frontController = $this->getResource('FrontController');
        $wordPress = $this->getResource('WordPress');
        $request = new \Zend_Controller_Request_Http();
        
        $request->setParam('wordpressQuery', $wordPress->query());
        $frontController->setRequest($request);
    }

    protected function _initRoutes()
    {
        $this->registerPluginResource('Router');
        $this->bootstrap(array('Autoloader', 'Router'));
        $router = $this->getResource('Router'); /* @var \Zend_Controller_Router_Rewrite $router */

        $router->addRoute(
            'default',
            new \Zend_Controller_Router_Route(
                '',
                array('module' => 'xend-theme', 'controller' => 'index', 'action' => 'index')
            ));

        $router->addRoute(
            'single',
            new \XendTheme\Controller\Router\Route\WordPress(
                array('single' => true),
                array('module' => 'xend-theme', 'controller' => 'index', 'action' => 'single')
            ));
    }

    protected function _initTheme()
    {
        $this->bootstrap('Autoloader', 'FrontController', 'Routes');
        $frontController = $this->getResource('FrontController');

        $frontController->addControllerDirectory(__DIR__ . '/application/controllers', 'xend-theme');

        if (function_exists('xend_child') && ($options = xend_child()) instanceof \XendTheme\Options) {

            $dir = rtrim($options->getChildDirectory(), '/\\');
            if (!is_dir($dir)) {
                throw new Exception('Child theme definition must be a valid directory path');
            }

            // Add the child theme module
            $frontController->addControllerDirectory($dir . '/controllers', $options->getChildModuleName());
            $frontController->setDefaultModule($options->getChildModuleName());

            // Bootstrap the child theme
            $this->registerPluginResource('Modules');
            $this->bootstrap('Modules');

        } else {
            $frontController->setDefaultModule('xend-theme')
               ->setParam('prefixDefaultModule', true);
            $options = new \XendTheme\Options(false);
        }

        return $options;
    }
    
    protected function _initWordPress()
    {
        $this->bootstrap(array('Autoloader', 'FrontController'));
        $wordpress = $this->getApplication()->getOption('xend');
        $frontController = $this->getResource('FrontController');
        $frontController->setParam('wordpress', $wordpress);
        return $wordpress;
    }
}
