<?php

namespace ImmortalchessNetBundle\Model;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.06.16
 * Time: 0:11
 */
class Post
{
    /**
     * @var int
     */
    private $forumId;

    /**
     * @var int
     */
    private $threadId;

    /**
     * @var int
     */
    private $firstThreadPostId;

    /**
     * @var string
     */
    private $lastPosterName;

    /**
     * @var int
     */
    private $lastPosterId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $pageText;

    /**
     * Post constructor.
     * @param int $forumId
     * @param int $threadId
     * @param string $lastPosterName
     * @param int $lastPosterId
     * @param string $title
     * @param string $pageText
     */
    public function __construct(
        $forumId,
        $threadId,
        $lastPosterName,
        $lastPosterId,
        $title,
        $pageText
    ) {
        $this->forumId = $forumId;
        $this->threadId = $threadId;
        $this->lastPosterName = $lastPosterName;
        $this->lastPosterId = $lastPosterId;
        $this->title = $title;
        $this->pageText = $pageText;
    }

    /**
     * @return int
     */
    public function getForumId()
    {
        return $this->forumId;
    }

    /**
     * @param int $forumId
     * @return Post
     */
    public function setForumId($forumId)
    {
        $this->forumId = $forumId;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param int $threadId
     * @return Post
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * @return int
     */
    public function getFirstThreadPostId()
    {
        return $this->firstThreadPostId;
    }

    /**
     * @param int $firstThreadPostId
     * @return Post
     */
    public function setFirstThreadPostId($firstThreadPostId)
    {
        $this->firstThreadPostId = $firstThreadPostId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastPosterName()
    {
        return $this->lastPosterName;
    }

    /**
     * @param string $lastPosterName
     * @return Post
     */
    public function setLastPosterName($lastPosterName)
    {
        $this->lastPosterName = $lastPosterName;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastPosterId()
    {
        return $this->lastPosterId;
    }

    /**
     * @param int $lastPosterId
     * @return Post
     */
    public function setLastPosterId($lastPosterId)
    {
        $this->lastPosterId = $lastPosterId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPageText()
    {
        return $this->pageText;
    }

    /**
     * @param string $pageText
     * @return Post
     */
    public function setPageText($pageText)
    {
        $this->pageText = $pageText;

        return $this;
    }
}