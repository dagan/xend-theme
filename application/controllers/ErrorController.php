<?php

/**
 * ErrorController
 *
 * @author Dagan
 */
class Xend_ErrorController extends \Xend\Controller\Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->title   = 'An Error Occurred';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->title   = 'Page Not Found';
                $this->view->message = 'The page you are looking for could not be found.';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->title   = 'Application Error';
                $this->view->message = 'An unexpected error occurred. Please try back again later.';
                break;
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
                $this->view->exception = $errors->exception;
    }
}
