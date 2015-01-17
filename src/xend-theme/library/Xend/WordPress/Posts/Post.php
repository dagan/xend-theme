<?php

namespace Xend\WordPress\Posts;

/**
 * Post
 *
 * @author Dagan
 * @property int       $id
 * @property int       $parentId
 * @property string    $guid
 * @property string    $type
 * @property string    $title
 * @property string    $mimeType
 * @property int       $authorId
 * @property \DateTime $date
 * @property \DateTime $dateGmt
 * @property string    $excerpt
 * @property string    $filteredExcerpt
 * @property string    $content
 * @property string    $filteredContent
 * @property string    $status
 * @property string    $commentStatus
 * @property string    $pingStatus
 * @property string    $password
 * @property \DateTime $modified
 * @property \DateTime $modifiedGmt
 * @property int       $commentCount
 * @property int       $menuOrder
 * @property \Xend\WordPress\User\User $author
 */
class Post
{    
    /**
     * The WordPress post object
     * @var \WP_Post
     */
    protected $_post;

    public function __construct(\WP_Post $post)
    {
        $this->_post = $post;
    }

    public function __get($property)
    {
        if (method_exists($this, $method = '_get' . ucfirst($property))) {
            return $this->$method();
        } else {
            $prop = "post_"
                    . preg_replace_callback("([A-Z])", function ($matches) { return "_" . strtolower($matches[0]); }, $property);
            return (isset($this->_post->{$prop})) ? $this->_post->{$prop} : null;
        }
    }

    protected function _getId()
    {
        return $this->_post->ID;
    }
    
    protected function _getParentId()
    {
        return $this->_post->post_parent;
    }
    
    protected function _getSlug()
    {
        return $this->_post->post_name;
    }
    
    protected function _getAuthorId()
    {
        return (int)$this->_post->post_author;
    }
    
    protected function _getCommentCount() {
        return $this->_post->comment_count;
    }
}
