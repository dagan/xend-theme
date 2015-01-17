<?php

namespace Xend\WordPress\Posts;

class PostContext extends Post {
    
    protected $_author;
    
    /**
     * @var PostsInterface
     */
    protected $_posts;
    
    /**
     * @var \Xend\WordPress\Events\EventsInterface
     */
    protected $_events;
    
    public function __construct(PostsInterface $posts, \Xend\WordPress\Events\EventsInterface $events, 
         \WP_User $author, \WP_Post $post) {
        
        parent::__construct($post);
        if (isset($author)) {
            $this->_author = new \Xend\WordPress\Users\User($author);
        }
        $this->_posts  = $posts;
        $this->_events = $events;
    }
    
    protected function _getAuthor() {
        return $this->_author;
    }
    
    public function getTheTitle() {
        return $this->_posts->getFilteredTitle($this);
    }
    
    public function theTitle() {
        echo $this->getTheTitle();
    }
    
    public function getThePermalink() {
        return $this->_posts->getFilteredPermalink($this);
    }
    
    public function thePermalink() {
        echo $this->_events->applyFilters('the_permalink', $this->getThePermalink());
    }
    
    public function getTheCommentsLink() {
        return $this->_posts->getFilteredCommentsLink($this);
    }
    
    public function theCommentsLink() {
        echo $this->_events->applyFilters('the_comments_link', $this->getTheCommentsLink());
    }
    
    public function getTheAuthor() {
        return $this->_posts->getFilteredAuthor($this);
    }
    
    public function theAuthor() {
        echo $this->getTheAuthor();
    }
    
    public function getTheExcerpt() {
        return $this->_posts->getFilteredExcerpt($this);
    }
    
    public function theExcerpt() {
        echo $this->_events->applyFilters('the_excerpt', $this->getTheExcerpt());
    }
    
    public function getTheContent() {
        return $this->_filteredContent = $this->_posts->getFilteredContent($this);
    }
    
    public function theContent() {
        echo $this->_events->applyFilters('the_content', $this->getTheContent());
    }
}