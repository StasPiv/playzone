<?php

namespace ImmortalchessNetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forum
 *
 * @ORM\Table(name="forum")
 * @ORM\Entity(repositoryClass="ImmortalchessNetBundle\Repository\ForumRepository")
 */
class Forum
{
    /**
     * @var integer
     *
     * @ORM\Column(name="forumid", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $forumid;

    /**
     * @var integer
     *
     * @ORM\Column(name="styleid", type="smallint", nullable=false)
     */
    private $styleid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="title_clean", type="string", length=100, nullable=false)
     */
    private $titleClean = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=16777215, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="description_clean", type="text", length=16777215, nullable=true)
     */
    private $descriptionClean;

    /**
     * @var integer
     *
     * @ORM\Column(name="options", type="integer", nullable=false)
     */
    private $options = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="showprivate", type="boolean", nullable=false)
     */
    private $showprivate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="displayorder", type="smallint", nullable=false)
     */
    private $displayorder = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="replycount", type="integer", nullable=false)
     */
    private $replycount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpost", type="integer", nullable=false)
     */
    private $lastpost = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lastposter", type="string", length=100, nullable=false)
     */
    private $lastposter = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpostid", type="integer", nullable=false)
     */
    private $lastpostid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lastthread", type="string", length=250, nullable=false)
     */
    private $lastthread = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastthreadid", type="integer", nullable=false)
     */
    private $lastthreadid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lasticonid", type="smallint", nullable=false)
     */
    private $lasticonid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lastprefixid", type="string", length=25, nullable=false)
     */
    private $lastprefixid = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="threadcount", type="integer", nullable=false)
     */
    private $threadcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="daysprune", type="smallint", nullable=false)
     */
    private $daysprune = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="newpostemail", type="text", length=16777215, nullable=true)
     */
    private $newpostemail;

    /**
     * @var string
     *
     * @ORM\Column(name="newthreademail", type="text", length=16777215, nullable=true)
     */
    private $newthreademail;

    /**
     * @var integer
     *
     * @ORM\Column(name="parentid", type="smallint", nullable=false)
     */
    private $parentid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="parentlist", type="string", length=250, nullable=false)
     */
    private $parentlist = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50, nullable=false)
     */
    private $password = '';

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=200, nullable=false)
     */
    private $link = '';

    /**
     * @var string
     *
     * @ORM\Column(name="childlist", type="text", length=16777215, nullable=true)
     */
    private $childlist;

    /**
     * @var string
     *
     * @ORM\Column(name="defaultsortfield", type="string", length=50, nullable=false)
     */
    private $defaultsortfield = 'lastpost';

    /**
     * @var string
     *
     * @ORM\Column(name="defaultsortorder", type="string", nullable=false)
     */
    private $defaultsortorder = 'desc';

    /**
     * @var string
     *
     * @ORM\Column(name="imageprefix", type="string", length=100, nullable=false)
     */
    private $imageprefix = '';

    /**
     * @return int
     */
    public function getForumid()
    {
        return $this->forumid;
    }

    /**
     * @param int $forumid
     * @return Forum
     */
    public function setForumid($forumid)
    {
        $this->forumid = $forumid;

        return $this;
    }

    /**
     * @return int
     */
    public function getStyleid()
    {
        return $this->styleid;
    }

    /**
     * @param int $styleid
     * @return Forum
     */
    public function setStyleid($styleid)
    {
        $this->styleid = $styleid;

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
     * @return Forum
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleClean()
    {
        return $this->titleClean;
    }

    /**
     * @param string $titleClean
     * @return Forum
     */
    public function setTitleClean($titleClean)
    {
        $this->titleClean = $titleClean;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Forum
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionClean()
    {
        return $this->descriptionClean;
    }

    /**
     * @param string $descriptionClean
     * @return Forum
     */
    public function setDescriptionClean($descriptionClean)
    {
        $this->descriptionClean = $descriptionClean;

        return $this;
    }

    /**
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param int $options
     * @return Forum
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowprivate()
    {
        return $this->showprivate;
    }

    /**
     * @param boolean $showprivate
     * @return Forum
     */
    public function setShowprivate($showprivate)
    {
        $this->showprivate = $showprivate;

        return $this;
    }

    /**
     * @return int
     */
    public function getDisplayorder()
    {
        return $this->displayorder;
    }

    /**
     * @param int $displayorder
     * @return Forum
     */
    public function setDisplayorder($displayorder)
    {
        $this->displayorder = $displayorder;

        return $this;
    }

    /**
     * @return int
     */
    public function getReplycount()
    {
        return $this->replycount;
    }

    /**
     * @param int $replycount
     * @return Forum
     */
    public function setReplycount($replycount)
    {
        $this->replycount = $replycount;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastpost()
    {
        return $this->lastpost;
    }

    /**
     * @param int $lastpost
     * @return Forum
     */
    public function setLastpost($lastpost)
    {
        $this->lastpost = $lastpost;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastposter()
    {
        return $this->lastposter;
    }

    /**
     * @param string $lastposter
     * @return Forum
     */
    public function setLastposter($lastposter)
    {
        $this->lastposter = $lastposter;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastpostid()
    {
        return $this->lastpostid;
    }

    /**
     * @param int $lastpostid
     * @return Forum
     */
    public function setLastpostid($lastpostid)
    {
        $this->lastpostid = $lastpostid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastthread()
    {
        return $this->lastthread;
    }

    /**
     * @param string $lastthread
     * @return Forum
     */
    public function setLastthread($lastthread)
    {
        $this->lastthread = $lastthread;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastthreadid()
    {
        return $this->lastthreadid;
    }

    /**
     * @param int $lastthreadid
     * @return Forum
     */
    public function setLastthreadid($lastthreadid)
    {
        $this->lastthreadid = $lastthreadid;

        return $this;
    }

    /**
     * @return int
     */
    public function getLasticonid()
    {
        return $this->lasticonid;
    }

    /**
     * @param int $lasticonid
     * @return Forum
     */
    public function setLasticonid($lasticonid)
    {
        $this->lasticonid = $lasticonid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastprefixid()
    {
        return $this->lastprefixid;
    }

    /**
     * @param string $lastprefixid
     * @return Forum
     */
    public function setLastprefixid($lastprefixid)
    {
        $this->lastprefixid = $lastprefixid;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadcount()
    {
        return $this->threadcount;
    }

    /**
     * @param int $threadcount
     * @return Forum
     */
    public function setThreadcount($threadcount)
    {
        $this->threadcount = $threadcount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDaysprune()
    {
        return $this->daysprune;
    }

    /**
     * @param int $daysprune
     * @return Forum
     */
    public function setDaysprune($daysprune)
    {
        $this->daysprune = $daysprune;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewpostemail()
    {
        return $this->newpostemail;
    }

    /**
     * @param string $newpostemail
     * @return Forum
     */
    public function setNewpostemail($newpostemail)
    {
        $this->newpostemail = $newpostemail;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewthreademail()
    {
        return $this->newthreademail;
    }

    /**
     * @param string $newthreademail
     * @return Forum
     */
    public function setNewthreademail($newthreademail)
    {
        $this->newthreademail = $newthreademail;

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
     * @return Forum
     */
    public function setParentid($parentid)
    {
        $this->parentid = $parentid;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentlist()
    {
        return $this->parentlist;
    }

    /**
     * @param string $parentlist
     * @return Forum
     */
    public function setParentlist($parentlist)
    {
        $this->parentlist = $parentlist;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Forum
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Forum
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getChildlist()
    {
        return $this->childlist;
    }

    /**
     * @param string $childlist
     * @return Forum
     */
    public function setChildlist($childlist)
    {
        $this->childlist = $childlist;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultsortfield()
    {
        return $this->defaultsortfield;
    }

    /**
     * @param string $defaultsortfield
     * @return Forum
     */
    public function setDefaultsortfield($defaultsortfield)
    {
        $this->defaultsortfield = $defaultsortfield;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultsortorder()
    {
        return $this->defaultsortorder;
    }

    /**
     * @param string $defaultsortorder
     * @return Forum
     */
    public function setDefaultsortorder($defaultsortorder)
    {
        $this->defaultsortorder = $defaultsortorder;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageprefix()
    {
        return $this->imageprefix;
    }

    /**
     * @param string $imageprefix
     * @return Forum
     */
    public function setImageprefix($imageprefix)
    {
        $this->imageprefix = $imageprefix;

        return $this;
    }


}

