<?php

namespace Xend\WordPress\Query;

class CommentIterator implements \Iterator {
 
    protected $_valid;
    
    /**
     * @var \WP_Query
     */
    protected $_query;
    
    /**
     * @var \Xend\WordPress\Posts
     */
    protected $_posts;
    
    /**
     * @var \Xend\WordPress\Events\EventsInterface
     */
    protected $_events;
    
    public function __construct(\WP_Query $query, \Xend\WordPress\Posts\PostsInterface $posts,
        \Xend\WordPress\Events\EventsInterface $events) {
        
        $this->_query  = $query;
        $this->_posts  = $posts;
        $this->_events = $events;
    }
    
    public function rewind() {
        $this->_query->rewind_comments();
        
        // Start the loop at the first post if there's any available
        if ($this->_query->have_comments()) {
            $this->_valid = true;
            $this->_query->the_comment();
        }
    }
    
    public function valid() {
        return $this->_valid;
    }
    
    public function current() {
        return new \Xend\WordPress\Posts\CommentContext($this->_posts, $this->_events,
            $this->_query->comments[$this->_query->current_comment]);
    }
    
    public function key() {
        return $this->_query->current_comment;
    }
    
    public function next() {
        if ($this->_query->have_comments()) {
            $this->_valid = true;
            $this->_query->the_comment();
        } else {
            $this->_valid = false;
        }
    }
}