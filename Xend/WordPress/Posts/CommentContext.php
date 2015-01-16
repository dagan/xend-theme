<?php

namespace Xend\WordPress\Posts;

class CommentContext extends Comment {
    
    /**
     * @var PostsInterface
     */
    protected $_posts;
    
    /**
     * @var \Xend\WordPress\Events\EventsInterface
     */
    protected $_events;
    
    public function __construct(PostsInterface $posts, \Xend\WordPress\Events\EventsInterface $events, $comment) {
        parent::__construct($comment);
        $this->_posts  = $posts;
        $this->_events = $events;
    }
    
    public function getTheAuthor() {
        return $this->_posts->getFilteredCommentAuthor($this);
    }
    
    public function theAuthor() {
        echo $this->_events->applyFilters('comment_author', $this->getTheAuthor(), $this->id);
    } 
    
    public function getTheAuthorEmail() {
        return $this->_posts->getFilteredCommentAuthorEmail($this);
    }
    
    public function theAuthorEmail() {
        echo $this->_events->applyFilters('author_email', $this->getTheAuthorEmail(), $this->id);
    }
    
    public function getTheAuthorIp() {
        return $this->_posts->getFilteredCommentAuthorIp($this);
    }
    
    public function theAuthorIp() {
        echo $this->getTheAuthorIp();
    }
    
    public function getTheAuthorUrl() {
        return $this->_posts->getFilteredCommentAuthorUrl($this);
    }
    
    public function theAuthorUrl() {
        echo $this->_events->applyFilters('comment_url', $this->getTheAuthorUrl(), $this->id);
    }
    
    public function getTheExcerpt() {
        return $this->_posts->getFilteredCommentExcerpt($this);
    }
    
    public function theExcerpt() {
        echo $this->_events->applyFilters('comment_excerpt', $this->getTheExcerpt(), $this->id);
    }
    
    public function getTheContent() {
        return $this->_posts->getFilteredCommentContent($this);
    }
    
    public function theContent() {
        echo $this->_events->applyFilters('comment_text', $this->getTheContent(), $this);
    }
}