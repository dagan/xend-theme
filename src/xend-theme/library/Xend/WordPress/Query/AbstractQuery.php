<?php

namespace Xend\WordPress\Query;

/**
 * Query
 *
 * @author Dagan
 */
abstract class AbstractQuery implements QueryInterface {
    
    /**
     * @var \WP_Query
     */
    protected $_wp_query;
    
    /**
     * @var \Xend\WordPress\Posts
     */
    protected $_posts;
    
    /**
     * @var \Xend\WordPress\Events
     */
    protected $_events;

    /**
     * @param \WP_Query $query
     */
    public function __construct(\WP_Query $query, \Xend\WordPress\Posts $posts, \Xend\WordPress\Events $events)
    {
        $this->_wp_query = $query;
        $this->_posts    = $posts;
        $this->_events   = $events;
    }

    public function __get($value)
    {
        if (method_exists($this, $method = 'get' . $this->_getMethodNameBase($value))) {
            return $this->$method();
        } else {
            return (isset($this->_wp_query->$value)) ? $this->_wp_query->$value : null;
        }
    }

    protected function _getMethodNameBase($value)
    {
        return preg_replace_callback(
                '/_([a-z])/',
                function ($matches) { return strtoupper($matches[1]); },
                preg_replace('/\W/', '_', ucfirst(strtolower($value))));
    }
    
    public function posts() {
        return new PostIterator($this->_wp_query, $this->_posts, $this->_events);
    }
    
    public function hasPosts() {
        return $this->_wp_query->post_count > 0;
    }
    
    public function countPosts() {
        return $this->_wp_query->post_count;
    }
    
    public function countTotalPosts() {
        return $this->_wp_query->found_posts;
    }
    
    public function comments() {
        // TODO Add support for unapproved comments posted by the current user/visitor
        
        $query = new \WP_Comment_Query();
        $comments = $query->query(array(
            'order'   => 'ASC',
    		'orderby' => 'comment_date_gmt',
    		'status'  => 'approve',
    		'post_id' => $this->_wp_query->post->ID));
        
        $this->_wp_query->current_comment = -1;
        $this->_wp_query->comment_count   = count($comments);
        $this->_wp_query->comments        = $comments;
        
        return new CommentIterator($this->_wp_query, $this->_posts, $this->_events);
    }
    
    public function hasComments() {
        return $this->countComments() > 0;
    }
    
    public function countComments() {
        if ($this->_wp_query->post instanceof \WP_Post) {
            return $this->_wp_query->post->comment_count;
        } else {
            return 0;
        }
    }

    public function getQueryType($include_subtype = false)
    {
        // 404
        if ($this->is404()) {
            $type = 'error';
            $subtype = '404';
        }

        // Admin
        else if ($this->isAdmin()) {
            $type = 'admin';
            $subtype = null;
        }

        // Comment Feeds
        else if ($this->isCommentFeed()) {
            $type = 'comment';
            $subtype = 'feed';
        }

        // Comment Popup
        else if ($this->isCommentsPopup()) {
            $type = 'comment';
            $subtype = 'popup';
        }

        // Home Page (page of posts)
        else if ($this->isHome()) {
            $type = 'index';
            $subtype = 'home';
        }

        // Static Front Page
        else if ($this->isFrontPage()) {
            $type = 'index';
            $subtype = 'front';
        }

        // Pages and Posts
        else if ($this->isSingular()) {
            $type = 'single';
            $obj = $this->getQueriedObject();
            switch ($obj->post_type) {
                case 'post':
                    $subtype = 'post';
                    break;
                case 'page':
                    $subtype = 'page';
                    break;
                case 'attachment':
                    $subtype = 'attachment';
                    break;
                default:
                    $subtype = $obj->post_type;
                    break;
            }
        }

        // Custom Post-Type Arvhives
        else if ($this->isPostTypeArchive()) {
            $type = 'archive';
            $subtype = $this->getQueriedObject()->slug;
        }

        // Categories
        else if ($this->isCategory()) {
            $type = 'category';
            $subtype = $this->getQueriedObject()->slug;
        }

        // Tags
        else if ($this->isTag()) {
            $type = 'tag';
            $subtype = $this->getQueriedObject()->slug;
        }

        // Taxonomies
        else if ($this->isTax()) {
            $type = 'taxonomy';
            $subtype = $this->getQueriedObject()->slug;
        }

        // Search
        else if ($this->isSearch()) {
            $type = 'search';
            $post_type = $this->_wp_query->get('post_type');
            $subtype = (!empty($post_type)) ? $post_type : 'index';
        }

        // Authors
        else if ($this->isAuthor()) {
            $type = 'author';
            $subtype = $this->getQueriedObject()->id;
        }

        // Dates
        else if ($this->isDate()) {
            $type = 'date';

            // Time
            if ($this->isTime()) {
                $subtype = 'time';
            }

            // Days
            else if ($this->isDay()) {
                $subtype = 'day';
            }

            // Months
            else if ($this->isMonth()) {
                $subtype = 'month';
            }

            // Years
            else if ($this->isYear()) {
                $subtype = 'year';
            }

            else {
                $subtype = 'index';
            }
        }

        // Feeds
        else if ($this->isFeed()) {
            $feed = $this->get('feed');
            if ('feed' == $feed) {
                $feed = get_default_feed();
            }

            $post_type = $this->get('post_type');
            $subtype = (empty($post_type)) ? 'feed' : $post_type;
        }

        // All other Archives
        else {
            $type = 'archive';
            $subtype = $this->getQueriedObject()->slug;
        }

        return ($include_subtype) ? array($type, $subtype, 'type' => $type, 'subtype' => $subtype) : $type;
    }

    /**
     * Returns the Currently Queried Object
     *
     * @return \stdClass|null
     */
    public function getQueriedObject()
    {
        return $this->_wp_query->get_queried_object();
    }

    public function is404()
    {
        return $this->_wp_query->is_404;
    }

    public function isAdmin()
    {
        return $this->_wp_query->is_admin;
    }

    public function isArchive()
    {
        return $this->_wp_query->is_archive;
    }

    public function isAttachment()
    {
        return $this->_wp_query->is_attachment;
    }

    public function isAuthor()
    {
        return $this->_wp_query->is_author;
    }

    public function isCategory()
    {
        return $this->_wp_query->is_category;
    }

    public function isCommentFeed()
    {
        return $this->_wp_query->is_comment_feed;
    }

    public function isCommentsPopup()
    {
        return $this->_wp_query->is_comments_popup;
    }

    public function isDate()
    {
        return $this->_wp_query->is_date;
    }

    public function isDay()
    {
        return $this->_wp_query->is_day;
    }

    public function isFeed()
    {
        return $this->_wp_query->is_feed;
    }

    public function isFrontPage()
    {
        return $this->_wp_query->is_front_page();
    }

    public function isHome()
    {
        return $this->_wp_query->is_home;
    }

    public function isMonth()
    {
        return $this->_wp_query->is_month;
    }

    public function isPage()
    {
        return $this->_wp_query->is_page;
    }

    public function isPaged()
    {
        return $this->_wp_query->is_paged;
    }

    public function isPost()
    {
        return $this->_wp_query->is_post;
    }

    public function isPostsPage()
    {
        return $this->_wp_query->is_posts_page;
    }

    public function isPostTypeArchive()
    {
        return $this->_wp_query->is_post_type_archive();
    }

    public function isPreview()
    {
        return $this->_wp_query->is_preview;
    }

    public function isRobots()
    {
        return $this->_wp_query->is_robots;
    }

    public function isSearch()
    {
        return $this->_wp_query->is_search;
    }

    public function isSingle()
    {
        return $this->_wp_query->is_single;
    }

    public function isSingular()
    {
        return $this->_wp_query->is_singular;
    }

    public function isTag()
    {
        return $this->_wp_query->is_tag;
    }

    public function isTax()
    {
        return $this->isTaxonomy();
    }

    public function isTaxonomy()
    {
        return $this->_wp_query->is_tax;
    }

    public function isTime()
    {
        return $this->_wp_query->is_time;
    }

    public function isTrackback()
    {
        return $this->_wp_query->is_trackback;
    }

    public function isYear()
    {
        return $this->_wp_query->is_year;
    }

    public function inTheLoop()
    {
        return ($this->_wp_query->in_the_loop) ? true : false;
    }
}
