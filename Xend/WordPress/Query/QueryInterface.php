<?php

namespace Xend\WordPress\Query;

/**
 * Query
 *
 * @author Dagan
 */
interface QueryInterface {

    /**
     * Check Whether the Query Has Posts
     * @return bool
     */
    public function hasPosts();
    
    /**
     * Retrieve the Number of Posts in the Query
     * @return int
     */
    public function countPosts();
    
    /**
     * Retrieve the Total Number of Posts in the Query
     * @return int
     */
    public function countTotalPosts();
    
    /**
     * Retireve a Posts Iterator
     * @return \Iterator
     */
    public function posts();
    
    /**
     * Check Whether the Post Has Comments
     * @return bool
     */
    public function hasComments();
    
    /**
     * Retrieve the Number of Comments on the Current Post
     * @return int
     */
    public function countComments();
    
    /**
     * Retrieve a Comments Iterator
     * @return \Iterator
    */
    public function comments();

    /**
     * Returns the Currently Queried Object
     *
     * @return \stdClass|null
     */
    public function getQueriedObject();
    
    /**
     * Retrieve the Query Type
     * @param bool $include_subtype
     * @return string|array
     */
    public function getQueryType($include_subtype = false);

    public function is404();

    public function isAdmin();

    public function isArchive();

    public function isAttachment();

    public function isAuthor();

    public function isCategory();

    public function isCommentFeed();

    public function isCommentsPopup();

    public function isDate();

    public function isDay();

    public function isFeed();

    public function isFrontPage();

    public function isHome();

    public function isMonth();

    public function isPage();

    public function isPaged();

    public function isPost();

    public function isPostsPage();

    public function isPostTypeArchive();

    public function isPreview();

    public function isRobots();

    public function isSearch();

    public function isSingle();
    
    public function isSingular();

    public function isTag();

    public function isTax();

    public function isTaxonomy();

    public function isTime();

    public function isTrackback();

    public function isYear();

    public function inTheLoop();
}
