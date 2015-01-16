<?php

namespace Xend\WordPress\Posts;

interface PostsInterface {
    
    /**
     * Retrieve a Post By ID
     * @param int  $id
     * @return Xend\WordPress\Posts\Post|bool
     */
    public function getPost($id);
    
    /**
     * Retrieve a Post's Author
     * @param  int|Post $post
     * @return Xend\WordPress\Users\User|bool
     */
    public function getAuthor($post);

    /**
     * Retrieve a Post's Comments
     * @param  int|Post $post
     * @return array|bool
     */
    public function getComments($post);
    
    /**
     * Retrieve a Post's Filtered Permalink
     * @param Post $post
     * @return string
     */
    public function getFilteredPermalink(Post $post);
    
    /**
     * Retrieve a Post's Filtered Comments Link
     * @param Post $post
     */
    public function getFilteredCommentsLink(Post $post);
    
    /**
     * Retireve a Post's Filtered Title
     * @param Post $post
     * @return string
     */
    public function getFilteredTitle(Post $post);
    
    /**
     * Retrieve a Post's Filtered Author Display Name
     * @param Post $post
     * @return string
     */
    public function getFilteredAuthor(Post $post);
    
    /**
     * Retreive a Posts's Filtered Excerpt
     * @param Post $post
     * @return string
     */
    public function getFilteredExcerpt(Post $post);
    
    /**
     * Retrieve a Post's Filtered Content
     * @param Post $post
     * @return string
     */
    public function getFilteredContent(Post $post);
    
    /**
     * Retrieve a Comment's Filtered Author Display Name
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentAuthor(Comment $comment);
    
    /**
     * Retrieve a Comments' Filtered Author Email
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentAuthorEmail(Comment $comment);
    
    /**
     * Retrieve a Comments' Filtered Author URL
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentAuthorUrl(Comment $comment);
    
    /**
     * Retrieve a Comments' Filtered Author IP
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentAuthorIp(Comment $comment);
    
    /**
     * Retrieve a Comments' Filtered Excerpt
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentExcerpt(Comment $comment);
    
    /**
     * Retrieve a Comments' Filtered Text
     * @param Comment $comment
     * @return string
     */
    public function getFilteredCommentContent(Comment $comment);
}