<?php

namespace ImmortalchessNetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thread
 *
 * @ORM\Table(name="thread", indexes={@ORM\Index(name="postuserid", columns={"postuserid"}), @ORM\Index(name="pollid", columns={"pollid"}), @ORM\Index(name="forumid", columns={"forumid", "visible", "sticky", "lastpost"}), @ORM\Index(name="lastpost", columns={"lastpost", "forumid"}), @ORM\Index(name="dateline", columns={"dateline"}), @ORM\Index(name="prefixid", columns={"prefixid", "forumid"}), @ORM\Index(name="title", columns={"title"})})
 * @ORM\Entity(repositoryClass="ImmortalchessNetBundle\Repository\ThreadRepository")
 */
class Thread
{
    /**
     * @var integer
     *
     * @ORM\Column(name="threadid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $threadid;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="prefixid", type="string", length=25, nullable=false)
     */
    private $prefixid = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="firstpostid", type="integer", nullable=false)
     */
    private $firstpostid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpostid", type="integer", nullable=false)
     */
    private $lastpostid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpost", type="integer", nullable=false)
     */
    private $lastpost = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="forumid", type="smallint", nullable=false)
     */
    private $forumid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pollid", type="integer", nullable=false)
     */
    private $pollid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="open", type="smallint", nullable=false)
     */
    private $open = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="replycount", type="integer", nullable=false)
     */
    private $replycount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="hiddencount", type="integer", nullable=false)
     */
    private $hiddencount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="deletedcount", type="integer", nullable=false)
     */
    private $deletedcount = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="postusername", type="string", length=100, nullable=false)
     */
    private $postusername = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="postuserid", type="integer", nullable=false)
     */
    private $postuserid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lastposter", type="string", length=100, nullable=false)
     */
    private $lastposter = '';

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
     * @var integer
     *
     * @ORM\Column(name="views", type="integer", nullable=false)
     */
    private $views = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="iconid", type="smallint", nullable=false)
     */
    private $iconid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=250, nullable=false)
     */
    private $notes = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="smallint", nullable=false)
     */
    private $visible = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="sticky", type="smallint", nullable=false)
     */
    private $sticky = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votenum", type="smallint", nullable=false)
     */
    private $votenum = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="votetotal", type="smallint", nullable=false)
     */
    private $votetotal = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="attach", type="smallint", nullable=false)
     */
    private $attach = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="similar", type="string", length=55, nullable=false)
     */
    private $similar = '';

    /**
     * @var string
     *
     * @ORM\Column(name="taglist", type="text", nullable=true)
     */
    private $taglist;

    /**
     * @var integer
     *
     * @ORM\Column(name="showfirstpost", type="integer", nullable=false)
     */
    private $showfirstpost = '0';

    /**
     * @return int
     */
    public function getThreadid()
    {
        return $this->threadid;
    }

    /**
     * @param int $threadid
     * @return Thread
     */
    public function setThreadid($threadid)
    {
        $this->threadid = $threadid;

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
     * @return Thread
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefixid()
    {
        return $this->prefixid;
    }

    /**
     * @param string $prefixid
     * @return Thread
     */
    public function setPrefixid($prefixid)
    {
        $this->prefixid = $prefixid;

        return $this;
    }

    /**
     * @return int
     */
    public function getFirstpostid()
    {
        return $this->firstpostid;
    }

    /**
     * @param int $firstpostid
     * @return Thread
     */
    public function setFirstpostid($firstpostid)
    {
        $this->firstpostid = $firstpostid;

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
     * @return Thread
     */
    public function setLastpostid($lastpostid)
    {
        $this->lastpostid = $lastpostid;

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
     * @return Thread
     */
    public function setLastpost($lastpost)
    {
        $this->lastpost = $lastpost;

        return $this;
    }

    /**
     * @return int
     */
    public function getForumid()
    {
        return $this->forumid;
    }

    /**
     * @param int $forumid
     * @return Thread
     */
    public function setForumid($forumid)
    {
        $this->forumid = $forumid;

        return $this;
    }

    /**
     * @return int
     */
    public function getPollid()
    {
        return $this->pollid;
    }

    /**
     * @param int $pollid
     * @return Thread
     */
    public function setPollid($pollid)
    {
        $this->pollid = $pollid;

        return $this;
    }

    /**
     * @return int
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @param int $open
     * @return Thread
     */
    public function setOpen($open)
    {
        $this->open = $open;

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
     * @return Thread
     */
    public function setReplycount($replycount)
    {
        $this->replycount = $replycount;

        return $this;
    }

    /**
     * @return int
     */
    public function getHiddencount()
    {
        return $this->hiddencount;
    }

    /**
     * @param int $hiddencount
     * @return Thread
     */
    public function setHiddencount($hiddencount)
    {
        $this->hiddencount = $hiddencount;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeletedcount()
    {
        return $this->deletedcount;
    }

    /**
     * @param int $deletedcount
     * @return Thread
     */
    public function setDeletedcount($deletedcount)
    {
        $this->deletedcount = $deletedcount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostusername()
    {
        return $this->postusername;
    }

    /**
     * @param string $postusername
     * @return Thread
     */
    public function setPostusername($postusername)
    {
        $this->postusername = $postusername;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostuserid()
    {
        return $this->postuserid;
    }

    /**
     * @param int $postuserid
     * @return Thread
     */
    public function setPostuserid($postuserid)
    {
        $this->postuserid = $postuserid;

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
     * @return Thread
     */
    public function setLastposter($lastposter)
    {
        $this->lastposter = $lastposter;

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
     * @return Thread
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
     * @return Thread
     */
    public function setLastedit($lastedit)
    {
        $this->lastedit = $lastedit;

        return $this;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $views
     * @return Thread
     */
    public function setViews($views)
    {
        $this->views = $views;

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
     * @return Thread
     */
    public function setIconid($iconid)
    {
        $this->iconid = $iconid;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Thread
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

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
     * @return Thread
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return int
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    /**
     * @param int $sticky
     * @return Thread
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * @return int
     */
    public function getVotenum()
    {
        return $this->votenum;
    }

    /**
     * @param int $votenum
     * @return Thread
     */
    public function setVotenum($votenum)
    {
        $this->votenum = $votenum;

        return $this;
    }

    /**
     * @return int
     */
    public function getVotetotal()
    {
        return $this->votetotal;
    }

    /**
     * @param int $votetotal
     * @return Thread
     */
    public function setVotetotal($votetotal)
    {
        $this->votetotal = $votetotal;

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
     * @return Thread
     */
    public function setAttach($attach)
    {
        $this->attach = $attach;

        return $this;
    }

    /**
     * @return string
     */
    public function getSimilar()
    {
        return $this->similar;
    }

    /**
     * @param string $similar
     * @return Thread
     */
    public function setSimilar($similar)
    {
        $this->similar = $similar;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaglist()
    {
        return $this->taglist;
    }

    /**
     * @param string $taglist
     * @return Thread
     */
    public function setTaglist($taglist)
    {
        $this->taglist = $taglist;

        return $this;
    }

    /**
     * @return int
     */
    public function getShowfirstpost()
    {
        return $this->showfirstpost;
    }

    /**
     * @param int $showfirstpost
     * @return Thread
     */
    public function setShowfirstpost($showfirstpost)
    {
        $this->showfirstpost = $showfirstpost;

        return $this;
    }


}

