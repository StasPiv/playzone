<?php

namespace ImmortalchessNetBundle\Entity;

use CoreBundle\Model\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="usergroupid", columns={"usergroupid"}), @ORM\Index(name="username", columns={"username"}), @ORM\Index(name="birthday", columns={"birthday", "showbirthday"}), @ORM\Index(name="birthday_search", columns={"birthday_search"}), @ORM\Index(name="referrerid", columns={"referrerid"}), @ORM\Index(name="post_thanks_thanked_times", columns={"post_thanks_thanked_times"}), @ORM\Index(name="posts", columns={"posts"}), @ORM\Index(name="lastactivity", columns={"lastactivity"})})
 * @ORM\Entity(repositoryClass="ImmortalchessNetBundle\Repository\ImmortalUserRepository")
 */
class ImmortalUser implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userid;

    /**
     * @var integer
     *
     * @ORM\Column(name="usergroupid", type="smallint", nullable=false)
     */
    private $usergroupid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="membergroupids", type="string", length=250, nullable=false)
     */
    private $membergroupids = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="displaygroupid", type="smallint", nullable=false)
     */
    private $displaygroupid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=false)
     */
    private $username = '';

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=false)
     */
    private $password = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="passworddate", type="date", nullable=false)
     */
    private $passworddate = '0000-00-00';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="styleid", type="smallint", nullable=false)
     */
    private $styleid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="parentemail", type="string", length=50, nullable=false)
     */
    private $parentemail = '';

    /**
     * @var string
     *
     * @ORM\Column(name="homepage", type="string", length=100, nullable=false)
     */
    private $homepage = '';

    /**
     * @var string
     *
     * @ORM\Column(name="icq", type="string", length=20, nullable=false)
     */
    private $icq = '';

    /**
     * @var string
     *
     * @ORM\Column(name="aim", type="string", length=20, nullable=false)
     */
    private $aim = '';

    /**
     * @var string
     *
     * @ORM\Column(name="yahoo", type="string", length=32, nullable=false)
     */
    private $yahoo = '';

    /**
     * @var string
     *
     * @ORM\Column(name="msn", type="string", length=100, nullable=false)
     */
    private $msn = '';

    /**
     * @var string
     *
     * @ORM\Column(name="skype", type="string", length=32, nullable=false)
     */
    private $skype = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="showvbcode", type="smallint", nullable=false)
     */
    private $showvbcode = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="showbirthday", type="smallint", nullable=false)
     */
    private $showbirthday = '2';

    /**
     * @var string
     *
     * @ORM\Column(name="usertitle", type="string", length=250, nullable=false)
     */
    private $usertitle = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="customtitle", type="smallint", nullable=false)
     */
    private $customtitle = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="joindate", type="integer", nullable=false)
     */
    private $joindate = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="daysprune", type="smallint", nullable=false)
     */
    private $daysprune = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastvisit", type="integer", nullable=false)
     */
    private $lastvisit = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastactivity", type="integer", nullable=false)
     */
    private $lastactivity = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpost", type="integer", nullable=false)
     */
    private $lastpost = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastpostid", type="integer", nullable=false)
     */
    private $lastpostid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="posts", type="integer", nullable=false)
     */
    private $posts = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="reputation", type="integer", nullable=false)
     */
    private $reputation = '10';

    /**
     * @var integer
     *
     * @ORM\Column(name="reputationlevelid", type="integer", nullable=false)
     */
    private $reputationlevelid = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="timezoneoffset", type="string", length=4, nullable=false)
     */
    private $timezoneoffset = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="pmpopup", type="smallint", nullable=false)
     */
    private $pmpopup = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="avatarid", type="smallint", nullable=false)
     */
    private $avatarid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="avatarrevision", type="integer", nullable=false)
     */
    private $avatarrevision = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="profilepicrevision", type="integer", nullable=false)
     */
    private $profilepicrevision = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="sigpicrevision", type="integer", nullable=false)
     */
    private $sigpicrevision = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="options", type="integer", nullable=false)
     */
    private $options = '33554447';

    /**
     * @var string
     *
     * @ORM\Column(name="birthday", type="string", length=10, nullable=false)
     */
    private $birthday = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday_search", type="date", nullable=false)
     */
    private $birthdaySearch = '0000-00-00';

    /**
     * @var integer
     *
     * @ORM\Column(name="maxposts", type="smallint", nullable=false)
     */
    private $maxposts = '-1';

    /**
     * @var integer
     *
     * @ORM\Column(name="startofweek", type="smallint", nullable=false)
     */
    private $startofweek = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="ipaddress", type="string", length=15, nullable=false)
     */
    private $ipaddress = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="referrerid", type="integer", nullable=false)
     */
    private $referrerid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="languageid", type="smallint", nullable=false)
     */
    private $languageid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="emailstamp", type="integer", nullable=false)
     */
    private $emailstamp = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="threadedmode", type="smallint", nullable=false)
     */
    private $threadedmode = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="autosubscribe", type="smallint", nullable=false)
     */
    private $autosubscribe = '-1';

    /**
     * @var integer
     *
     * @ORM\Column(name="pmtotal", type="smallint", nullable=false)
     */
    private $pmtotal = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pmunread", type="smallint", nullable=false)
     */
    private $pmunread = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=30, nullable=false)
     */
    private $salt = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="ipoints", type="integer", nullable=false)
     */
    private $ipoints = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="infractions", type="integer", nullable=false)
     */
    private $infractions = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="warnings", type="integer", nullable=false)
     */
    private $warnings = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="infractiongroupids", type="string", length=255, nullable=false)
     */
    private $infractiongroupids = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="infractiongroupid", type="smallint", nullable=false)
     */
    private $infractiongroupid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="adminoptions", type="integer", nullable=false)
     */
    private $adminoptions = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="profilevisits", type="integer", nullable=false)
     */
    private $profilevisits = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="friendcount", type="integer", nullable=false)
     */
    private $friendcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="friendreqcount", type="integer", nullable=false)
     */
    private $friendreqcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="vmunreadcount", type="integer", nullable=false)
     */
    private $vmunreadcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="vmmoderatedcount", type="integer", nullable=false)
     */
    private $vmmoderatedcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="socgroupinvitecount", type="integer", nullable=false)
     */
    private $socgroupinvitecount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="socgroupreqcount", type="integer", nullable=false)
     */
    private $socgroupreqcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pcunreadcount", type="integer", nullable=false)
     */
    private $pcunreadcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="pcmoderatedcount", type="integer", nullable=false)
     */
    private $pcmoderatedcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="gmmoderatedcount", type="integer", nullable=false)
     */
    private $gmmoderatedcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="post_thanks_user_amount", type="integer", nullable=false)
     */
    private $postThanksUserAmount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="post_thanks_thanked_posts", type="integer", nullable=false)
     */
    private $postThanksThankedPosts = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="post_thanks_thanked_times", type="integer", nullable=false)
     */
    private $postThanksThankedTimes = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="lastrepcheck", type="integer", nullable=false)
     */
    private $lastrepcheck;

    /**
     * @var string
     *
     * @ORM\Column(name="smilie", type="text", length=16777215, nullable=false)
     */
    private $smilie;

    /**
     * @var string
     *
     * @ORM\Column(name="qqwinpos", type="string", length=10, nullable=false)
     */
    private $qqwinpos = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="uploads", type="integer", nullable=false)
     */
    private $uploads;

    /**
     * @var integer
     *
     * @ORM\Column(name="downloads", type="integer", nullable=false)
     */
    private $downloads;

    /**
     * @var integer
     *
     * @ORM\Column(name="comments", type="integer", nullable=false)
     */
    private $comments;

    /**
     * @var integer
     *
     * @ORM\Column(name="bloggroupreqcount", type="integer", nullable=false)
     */
    private $bloggroupreqcount = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="showblogcss", type="integer", nullable=false)
     */
    private $showblogcss = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="showcybstats", type="boolean", nullable=false)
     */
    private $showcybstats = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="favsmilies", type="text", length=65535, nullable=false)
     */
    private $favsmilies;

    /**
     * @var string
     *
     * @ORM\Column(name="vbet_def_lang", type="string", length=5, nullable=true)
     */
    private $vbetDefLang;

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->getUserid();
    }

    /**
     * @inheritDoc
     */
    public function getLogin(): string
    {
        return $this->getUsername();
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
     * @return ImmortalUser
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;

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
     * @return ImmortalUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

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
     * @return ImmortalUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     * @return ImmortalUser
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return ImmortalUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


}

