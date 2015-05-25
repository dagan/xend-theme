<?php

namespace XendTheme\Controller\Router\Route;

/**
 * WordPressTest
 *
 * @author Dagan
 */
class WordPressTest extends \PHPUnit_Framework_TestCase {
    
    public function routeDataProvider() {
        return array(
            array(
                'single',
                'post',
                array('single' => true),
                array('module' => 'xend', 'controller' => 'index', 'action' => 'index')
            ),
            array(
                'single',
                'post',
                array('single' => 'post'),
                array('module' => 'xend', 'controller' => 'index', 'action' => 'post')
            ),
            array(
                'single',
                'page',
                array('single' => 'page'),
                array('module' => 'xend', 'controller' => 'index', 'action' => 'page')
            ),
        );
    }
    
    /**
     * @dataProvider routeDataProvider
     */
    public function testMatch($queryType, $querySubtype, $criteria, $params) {
        
        $query = $this->getMockBuilder('Xend\\WordPress\\Query')->disableOriginalConstructor()->getMock();
        $query->expects($this->once())
              ->method('getQueryType')
              ->with($this->equalTo(true))
              ->willReturn(array($queryType, $querySubtype));
        
        $request = new \Zend_Controller_Request_Http();
        $request->setParam('wordpressQuery', $query);
        
        $fixture = new \XendTheme\Controller\Router\Route\WordPress($criteria, $params);
        $this->assertEquals($params, $fixture->match($request));
    }
    
    public function testUseModule() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->useModule('xend');
        $params = $fixture->getParams();
        $this->assertEquals('xend', $params['module']);
    }
    
    public function testUseController() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->useController('my-controller');
        $params = $fixture->getParams();
        $this->assertEquals('my-controller', $params['controller']);
    }
    
    public function testUseAction() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->useAction('my-action');
        $params = $fixture->getParams();
        $this->assertEquals('my-action', $params['action']);
    }
    
    public function testUseParams() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        
        $fixture->setParams(array('controller' => 'my-controller', 'action' => 'my-action'));
        $fixture->useParams(array('someKey' => 'someValue'));
        $this->assertEquals(
            array('someKey' => 'someValue', 'controller' => 'my-controller', 'action' => 'my-action'),
            $fixture->getParams());
        
    }
    
    public function testIs404() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->is404();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('404', $criteria['error']);
    }
    
    public function testIsAdmin() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isAdmin();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['admin']);
    }
    
    public function testIsAttachment() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isAttachment();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('attachment', $criteria['single']);
    }
    
    public function testIsAuthor($author = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isAuthor();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['author']);
        
        $fixture->isAuthor(52);
        $criteria = $fixture->getCriteria();
        $this->assertEquals(52, $criteria['author']);
    }
    
    public function testIsCategory($category = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isCategory();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['category']);
        
        $fixture->isCategory('my-category');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-category', $criteria['category']);
    }
    
    public function testIsCommentsFeed() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isCommentsFeed();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('feed', $criteria['comment']);
    }
    
    public function testIsCommentsPopup() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isCommentsPopup();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('popup', $criteria['comment']);
    }
    
    public function testIsCustomPostTypeArchive($postType = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isCustomPostTypeArchive();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['archive']);
        
        $fixture->isCustomPostTypeArchive('my-post-type');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-post-type', $criteria['archive']);
    }
    
    public function testIsDate($type = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isDate();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['date']);
        
        $fixture->isDate('year');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('year', $criteria['date']);
    }
    
    public function testIsFeed($feed = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isFeed();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['feed']);
        
        $fixture->isFeed('my-post-type');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-post-type', $criteria['feed']);
    }
    
    public function testIsFrontPage() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isFrontPage();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('front', $criteria['index']);
    }
    
    public function testIsHome() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isHome();
        $criteria = $fixture->getCriteria();
        $this->assertEquals('home', $criteria['index']);
    }
    
    public function testIsSearch() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isSearch();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['search']);
        
        $fixture->isSearch('my-post-type');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-post-type', $criteria['search']);
    }
    
    public function testIsSingle() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isSingle();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['single']);
        
        $fixture->isSingle('my-post-type');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-post-type', $criteria['single']);
    }
    
    public function testIsTag() {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isTag();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['tag']);
        
        $fixture->isTag('my-tag');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-tag', $criteria['tag']);
    }
    
    public function isTaxonomy($taxonomy = true) {
        $fixture = new \XendTheme\Controller\Router\Route\WordPress();
        $fixture->isTaxonomy();
        $criteria = $fixture->getCriteria();
        $this->assertEquals(true, $criteria['tag']);
        
        $fixture->isTaxonomy('my-tax');
        $criteria = $fixture->getCriteria();
        $this->assertEquals('my-tax', $criteria['taxonomy']);
    }
}
