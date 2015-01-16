<?php

namespace Xend\WordPress\Posts;

use \Xend\WordPress\Exception;

abstract class AbstractPosts implements PostsInterface {
    
    /**
     * @var \Xend\WordPress\Events
     */
    protected $_events;
    
    public function __construct(\Xend\WordPress\Events\EventsInterface $events) {
        $this->_events = $events;
    }
    
    public function getPost($id) {
        
        if (!function_exists("get_post")) {
            throw new Exception("Native WordPress function get_post() is not defined");
        }
        
        $wp_post = get_post($id);
        return ($wp_post instanceof \WP_Post) ? new Post($wp_post) : false;
    }
    
    public function getAuthor($post) {
       
        if (!function_exists("get_user_by")) {
            throw new Exception("Native WordPress function get_user_by() is not defined");
        }
        
        if (is_int($post)) {
            $post = $this->getPost($id);
        }
        
        if ($post == false) {
            return false;
        }
        
        if (!$post instanceof \Xend\WordPress\Posts\Post) {
            throw new Exception("The first argument passed to \Xend\WordPress\Posts\DefaultPosts::getAuthor() must be "
                . "an integer or Post object");
        }
        
        $wp_user = get_user_by("id", $post->authorId);
        return ($wp_user instanceof \WP_User) ? new \Xend\WordPresU\Users\User($wp_user) : false;
    }
    
    public function getFilteredTitle(Post $post) {
        return $this->_events->applyFilters('the_title', $post->title, $post->id);
    }
    
    public function getFilteredAuthor(Post $post) {
        $author = ($post instanceof PostContext) ? $post->author : $this->getAuthor($post);
        return $this->_events->applyFilters('the_author', $author->displayName);
    }
    
    public function getFilteredExcerpt(Post $post) {
        return $this->_events->applyFilters('get_the_excerpt', $post->excerpt);
    }
    
    public function getFilteredContent(Post $post) {
        return $this->_events->applyFilters('get_the_content', $post->content);
    }
    
    public function getComments($post) {
        $query = new \WP_Comment_Query();
        $_comments = $query->query(array(
            'order'   => 'ASC',
            'orderby' => 'comment_date_gmt',
            'post_id' => ($post instanceof Post) ? $post->id : $post));
        
        $comments = array();
        foreach ($_comments as $_comment) {
            $comments[] = new Comment($_comment);
        }
        
        return $comments;
    }
    
    public function getFilteredPermalink(Post $post) {
        if (!function_exists("get_permalink")) {
            throw new Exception("Native WordPress function get_permalink() is not defined");
        }
        
        return get_permalink($post->id);
    }
    
    public function getFilteredCommentsLink(Post $post) {
        return $this->_events->applyFilters('get_comments_link', $this->getFilteredPermalink($post) . '#comments');
    }
    
    public function getFilteredCommentAuthor(Comment $comment) {
        return $this->_events->applyFilters('get_comment_author', $comment->author);
    }
    
    public function getFilteredCommentAuthorEmail(Comment $comment) {
        return $this->_events->applyFilters('get_comment_author_email', $comment->authorEmail);
    }
    
    public function getFilteredCommentAuthorUrl(Comment $comment) {
        return $this->_events->applyFilters('get_comment_author_url', $comment->authorUrl);
    }
    
    public function getFilteredCommentAuthorIp(Comment $comment) {
        return $this->_events->applyFilters('get_comment_author_IP', $comment->authorIp);
    }
    
    public function getFilteredCommentExcerpt(Comment $comment) {
        $excerpt = strip_tags($comment->content);
        $excerpt = str_replace("\r", '', $excerpt);
        $excerpt = str_replace("\n", ' ', $excerpt);
        
        // Reduce to first 20 words if $excerpt > 20 words
        $words = explode(' ', $excerpt);
        if (count($words) > 20) {
            $excerpt = implode(' ', array_slice($words, 0, 20)) . '&nbsp;&hellip;';
        }
        
        return $this->_events->applyFilters('get_comment_excerpt', $excerpt);
    }
    
    public function getFilteredCommentContent(Comment $comment) {
        return $this->_events->applyFilters('get_comment_text', $comment->content, $comment);
    }
}