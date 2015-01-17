<?php

namespace Xend\WordPress\Query;

class PostIterator implements \Iterator {
    
    protected $_valid;
    
    /**
     * @var \WP_Query
     */
    protected $_query;
    
    /**
     * @var \Xend\WordPress\Posts\PostsInterface
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
        $this->_query->rewind_posts();
        
        // Start the loop at the first post if there's any available
        if ($this->_query->have_posts()) {
            $this->_valid = true;
            $this->_query->the_post();
        }
    }
    
    public function valid() {
        return $this->_valid;
    }
    
    public function current() {
        global $authordata; // This is created/updated by WordPress after calling the_post()
        return new \Xend\WordPress\Posts\PostContext($this->_posts, $this->_events, $authordata, $this->_query->post);
    }
    
    public function key() {
        return $this->_query->current_post;
    }
    
    public function next() {
        if ($this->_query->have_posts()) {
            $this->_valid = true;
            return $this->_query->the_post();
        } else {
            $this->_valid = false;
        }
    }
}
