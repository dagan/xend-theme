<?php

namespace Xend\Controller\Router\Route;

/**
 * WordPress
 *
 * @author Dagan
 */
class WordPress implements \Zend_Controller_Router_Route_Interface
{
    public static function getInstance(\Zend_Config $config) {
        $config   = $config->toArray();
        $criteria = (array_key_exists('criteria', $config)) ? $config['criteria'] : array();
        $params   = (array_key_exists('params', $config)) ? $config['params'] : array();
        return new static($criteria, $params);
    }

    protected $_criteria;
    protected $_params;
    
    public function __construct($criteria = array(), $params = array())
    {
        if ($criteria instanceof \Zend_Config) {
            $criteria = $criteria->toArray();
        }
        $this->setCriteria($criteria);

        if ($params instanceof \Zend_Config) {
            $params = array();
        }
        $this->setParams($params);
    }

    public function getCriteria() {
        return $this->_criteria;
    }

    public function setCriteria(array $criteria) {
        $this->_criteria = $criteria;
        return $this;
    }

    public function getParams() {
        return $this->_params;
    }

    public function setParams(array $params) {
        $this->_params = $params;
        return $this;
    }

    public function useModule($module) {
        $this->_params['module'] = $module;
        return $this;
    }

    public function useController($controller) {
        $this->_params['controller'] = $controller;
        return $this;
    }

    public function useAction($action) {
        $this->_params['action'] = $action;
        return $this;
    }

    public function useParams(array $params) {
        $this->_params = array_merge($params, $this->_params);
        return $this;
    }

    public function is404() {
        $this->_criteria['error'] = '404';
        return $this;
    }

    public function isAdmin() {
        $this->_criteria['admin'] = true;
        return $this;
    }

    public function isAttachment() {
        $this->_criteria['single'] = 'attachment';
        return $this;
    }

    public function isAuthor($author = true) {
        $this->_criteria['author'] = $author;
    }

    public function isCategory($category = true) {
        $this->_criteria['category'] = $category;
        return $this;
    }

    public function isCommentsFeed() {
        $this->_criteria['comment'] = 'feed';
        return $this;
    }

    public function isCommentsPopup() {
        $this->_criteria['comment'] = 'popup';
        return $this;
    }

    public function isCustomPostTypeArchive($postType = true) {
        $this->_criteria['archive'] = $postType;
        return $this;
    }

    public function isDate($type = true) {
        $this->_criteria['date'] = $type;
        return $this;
    }

    public function isFeed($feed = true) {
        $this->_criteria['feed'] = $feed;
        return $this;
    }

    public function isFrontPage() {
        $this->_criteria['index'] = 'front';
        return $this;
    }

    public function isHome() {
        $this->_criteria['index'] = 'home';
        return $this;
    }

    public function isSearch($type = true) {
        $this->_criteria['search'] = $type;
        return $this;
    }

    public function isSingle($type = true) {
        $this->_criteria['single'] = $type;
        return $this;
    }

    public function isTag($tag = true) {
        $this->_criteria['tag'] = $tag;
        return $this;
    }

    public function isTaxonomy($taxonomy = true) {
        $this->_criteria['taxonomy'] = $taxonomy;
        return $this;
    }

    public function getVersion()
    {
        return 2;
    }

    public function assemble($data = array(), $reset = false, $encode = false)
    {
        // TODO Should utilize the defined WordPress permalink structures
    }

    public function match($request)
    {
        $query = $request->getParam('wordpressQuery');
        if (!$query instanceof \Xend\WordPress\Query) {
            return false;
        }

        list($type, $subtype) = $query->getQueryType(true);

        foreach ($this->getCriteria() as $requiredType => $requiredSubtype) {
            if ($type !== $requiredType) {
                return false;
            }

            if ($requiredSubtype !== true && $subtype !== $requiredSubtype) {
                return false;
            }
        }

        return $this->getParams();
    }
}
