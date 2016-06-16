<?php

namespace ImmortalchessNetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post", indexes={@ORM\Index(name="userid", columns={"userid"}), @ORM\Index(name="threadid", columns={"threadid", "userid"}), @ORM\Index(name="dateline", columns={"dateline"}), @ORM\Index(name="title", columns={"title", "pagetext"})})
 * @ORM\Entity(repositoryClass="ImmortalchessNetBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="postid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $postid;

    /**
     * @var integer
     *
     * @ORM\Column(name="threadid", type="integer", nullable=false)
     */
    private $threadid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="parentid", type="integer", nullable=false)
     */
    private $parentid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=false)
     */
    private $username = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $userid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250, nullable=false)
     */
    private $title = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="dateline", type="integer", nullable=false)
     */
    private $dateline = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastedit", type="integer", nullable=false)
     */
    private $lastedit = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="pagetext", type="text", nullable=true)
     */
    private $pagetext;

    /**
     * @var integer
     *
     * @ORM\Column(name="allowsmilie", type="smallint", nullable=false)
     */
    private $allowsmilie = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="showsignature", type="smallint", nullable=false)
     */
    private $showsignature = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ipaddress", type="string", length=15, nullable=false)
     */
    private $ipaddress = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="iconid", type="smallint", nullable=false)
     */
    private $iconid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     */
    private $visible = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="attach", type="smallint", nullable=false)
     */
    private $attach = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="infraction", type="smallint", nullable=false)
     */
    private $infraction = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="reportthreadid", type="integer", nullable=false)
     */
    private $reportthreadid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="post_thanks_amount", type="integer", nullable=false)
     */
    private $postThanksAmount = '0';

    /**
     * @return int
     */
    public function getPostid()
    {
        return $this->postid;
    }

    /**
     * @param int $postid
     * @return Post
     */
    public function setPostid($postid)
    {
        $this->postid = $postid;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadid()
    {
        return $this->threadid;
    }

    /**
     * @param int $threadid
     * @return Post
     */
    public function setThreadid($threadid)
    {
        $this->threadid = $threadid;

        return $this;
    }

    /**
     * @return int
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * @param int $parentid
     * @return Post
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Post
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param int $userid
     * @return Post
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

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
     * @return int
     */
    public function getDateline()
    {
        return $this->dateline;
    }

    /**
     * @param int $dateline
     * @return Post
     */
    public function setDateline($dateline)
    {
        $this->dateline = $dateline;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastedit()
    {
        return $this->lastedit;
    }

    /**
     * @param int $lastedit
     * @return Post
     */
    public function setLastedit($lastedit)
    {
        $this->lastedit = $lastedit;

        return $this;
    }

    /**
     * @return string
     */
    public function getPagetext()
    {
        return $this->pagetext;
    }

    /**
     * @param string $pagetext
     * @return Post
     */
    public function setPagetext($pagetext)
    {
        $this->pagetext = $pagetext;

        return $this;
    }

    /**
     * @return int
     */
    public function getAllowsmilie()
    {
        return $this->allowsmilie;
    }

    /**
     * @param int $allowsmilie
     * @return Post
     */
    public function setAllowsmilie($allowsmilie)
    {
        $this->allowsmilie = $allowsmilie;

        return $this;
    }

    /**
     * @return int
     */
    public function getShowsignature()
    {
        return $this->showsignature;
    }

    /**
     * @param int $showsignature
     * @return Post
     */
    public function setShowsignature($showsignature)
    {
        $this->showsignature = $showsignature;

        return $this;
    }

    /**
     * @return string
     */
    public function getIpaddress()
    {
        return $this->ipaddress;
    }

    /**
     * @param string $ipaddress
     * @return Post
     */
    public function setIpaddress($ipaddress)
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * @return int
     */
    public function getIconid()
    {
        return $this->iconid;
    }

    /**
     * @param int $iconid
     * @return Post
     */
    public function setIconid($iconid)
    {
        $this->iconid = $iconid;

        return $this;
    }

    /**
     * @return int
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param int $visible
     * @return Post
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return int
     */
    public function getAttach()
    {
        return $this->attach;
    }

    /**
     * @param int $attach
     * @return Post
     */
    public function setAttach($attach)
    {
        $this->attach = $attach;

        return $this;
    }

    /**
     * @return int
     */
    public function getInfraction()
    {
        return $this->infraction;
    }

    /**
     * @param int $infraction
     * @return Post
     */
    public function setInfraction($infraction)
    {
        $this->infraction = $infraction;

        return $this;
    }

    /**
     * @return int
     */
    public function getReportthreadid()
    {
        return $this->reportthreadid;
    }

    /**
     * @param int $reportthreadid
     * @return Post
     */
    public function setReportthreadid($reportthreadid)
    {
        $this->reportthreadid = $reportthreadid;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostThanksAmount()
    {
        return $this->postThanksAmount;
    }

    /**
     * @param int $postThanksAmount
     * @return Post
     */
    public function setPostThanksAmount($postThanksAmount)
    {
        $this->postThanksAmount = $postThanksAmount;

        return $this;
    }


}

