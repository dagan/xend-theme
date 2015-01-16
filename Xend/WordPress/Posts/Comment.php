<?php

namespace Xend\WordPress\Posts;

/**
 * Comment
 * 
 * @author dagan
 * @property int       $id
 * @property int       $parent
 * @property int       $postId
 * @property string    $authorDisplayName
 * @property string    $authorEmail
 * @property string    $authorUrl
 * @property string    $authorIp
 * @property \DateTime $date
 * @property \DateTime $dateGmt
 * @property string    $content
 * @property int       $karma
 * @property string    $approbation
 * @property string    $agent
 * @property string    $type
 */
class Comment {
    
    protected $_comment;
    
    public function __construct($comment) {
        $this->_comment = $comment;    
    }
    
    public function __get($property)
    {
        $getterMethod = '_get' . ucfirst($property);
        $option       = 'comment_'
                        . preg_replace_callback(
                            '([A-Z])',
                            function ($matches) { return '_' . strtolower($matches[0]); },
                            $property);
            
        if (method_exists($this, $getterMethod)) {
            return $this->$getterMethod();
        } elseif (isset($this->_comment->{$option})) {
            return $this->_comment->{$option};
        } elseif (isset($this->_comment->{$property})) {
            return null;
        }
    }
    
    protected function _getId() {
        return $this->_comment->comment_ID;
    }
    
    protected function _getParent() {
        return (int)$this->_comment->comment_parent;
    }
    
    protected function _getAuthorDisplayName() {
        return $this->_comment->comment_author;
    }
    
    protected function _getDate() {
        return new \DateTime($this->_comment->comment_date);
    }
    
    protected function _getDateGmt() {
        return new \DateTime($this->_comment->comment_date_gmt, new \DateTimeZone('GMT'));
    }
}