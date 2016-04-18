<?php

namespace CoreBundle\Entity;

use CoreBundle\Exception\Handler\User\UserSettingNotFoundException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * User
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\SerializedName("id")
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=255, unique=true)
     *
     * @JMS\Expose
     * @JMS\SerializedName("login")
     * @JMS\Type("string")
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @JMS\Expose
     * @JMS\SerializedName("email")
     * @JMS\Type("string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @var bool
     *
     * @ORM\Column(name="confirm", type="boolean", nullable=true)
     */
    private $confirm;

    /**
     * @var bool
     *
     * @ORM\Column(name="in_rest", type="boolean", nullable=true)
     */
    private $inRest;

    /**
     * @var int
     *
     * @ORM\Column(name="left_rest", type="integer", nullable=true)
     */
    private $leftRest;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gone_in_rest", type="datetime", nullable=true)
     */
    private $goneInRest;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=1)
     *
     * @JMS\Expose
     * @JMS\SerializedName("class")
     * @JMS\Type("string")
     */
    private $class = 'N';

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $token = '';

    /**
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     *
     * @JMS\Expose
     * @JMS\SerializedName("rating")
     * @JMS\Type("integer")
     */
    private $rating = 2200;

    /**
     * @var int
     *
     * @ORM\Column(name="win", type="integer")
     */
    private $win = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="draw", type="integer")
     */
    private $draw = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="lose", type="integer")
     */
    private $lose = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="lose_time", type="integer")
     */
    private $loseTime = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_auth", type="datetime", nullable=true)
     */
    private $lastAuth;

    /**
     * @var int
     *
     * @ORM\Column(name="immortal_id", type="integer", nullable=true)
     */
    private $immortalId;

    /**
     * @var string
     *
     * @ORM\Column(name="another_login", type="string", length=255, nullable=true)
     */
    private $anotherLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_move", type="datetime", nullable=true)
     */
    private $lastMove;

    /**
     * @var int
     *
     * @ORM\Column(name="balance", type="bigint")
     */
    private $balance = 0;

    /**
     * @JMS\Expose
     * @JMS\SerializedName("isAuth")
     * @JMS\Type("boolean")
     */
    private $auth = true;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $offline = false;

    /**
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\TournamentPlayer", mappedBy="player", cascade={"persist"})
     * 
     * @var PersistentCollection
     */
    private $tournaments;

    /**
     * @var UserSetting[]
     * 
     * @ORM\Column(type="array")
     * @JMS\Expose
     * @JMS\Type("array<CoreBundle\Entity\UserSetting>")
     */
    private $settings;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set login
     *
     * @param string $login
     *
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set confirm
     *
     * @param boolean $confirm
     *
     * @return User
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * Get confirm
     *
     * @return bool
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Set inRest
     *
     * @param boolean $inRest
     *
     * @return User
     */
    public function setInRest($inRest)
    {
        $this->inRest = $inRest;

        return $this;
    }

    /**
     * Get inRest
     *
     * @return bool
     */
    public function getInRest()
    {
        return $this->inRest;
    }

    /**
     * Set leftRest
     *
     * @param integer $leftRest
     *
     * @return User
     */
    public function setLeftRest($leftRest)
    {
        $this->leftRest = $leftRest;

        return $this;
    }

    /**
     * Get leftRest
     *
     * @return int
     */
    public function getLeftRest()
    {
        return $this->leftRest;
    }

    /**
     * Set goneInRest
     *
     * @param \DateTime $goneInRest
     *
     * @return User
     */
    public function setGoneInRest($goneInRest)
    {
        $this->goneInRest = $goneInRest;

        return $this;
    }

    /**
     * Get goneInRest
     *
     * @return \DateTime
     */
    public function getGoneInRest()
    {
        return $this->goneInRest;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return User
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return User
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set win
     *
     * @param integer $win
     *
     * @return User
     */
    public function setWin($win)
    {
        $this->win = $win;

        return $this;
    }

    /**
     * Get win
     *
     * @return int
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * Set draw
     *
     * @param integer $draw
     *
     * @return User
     */
    public function setDraw($draw)
    {
        $this->draw = $draw;

        return $this;
    }

    /**
     * Get draw
     *
     * @return int
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * Set lose
     *
     * @param integer $lose
     *
     * @return User
     */
    public function setLose($lose)
    {
        $this->lose = $lose;

        return $this;
    }

    /**
     * Get lose
     *
     * @return int
     */
    public function getLose()
    {
        return $this->lose;
    }

    /**
     * Set loseTime
     *
     * @param integer $loseTime
     *
     * @return User
     */
    public function setLoseTime($loseTime)
    {
        $this->loseTime = $loseTime;

        return $this;
    }

    /**
     * Get loseTime
     *
     * @return int
     */
    public function getLoseTime()
    {
        return $this->loseTime;
    }

    /**
     * Set lastAuth
     *
     * @param \DateTime $lastAuth
     *
     * @return User
     */
    public function setLastAuth($lastAuth)
    {
        $this->lastAuth = $lastAuth;

        return $this;
    }

    /**
     * Get lastAuth
     *
     * @return \DateTime
     */
    public function getLastAuth()
    {
        return $this->lastAuth;
    }

    /**
     * Set immortalId
     *
     * @param integer $immortalId
     *
     * @return User
     */
    public function setImmortalId($immortalId)
    {
        $this->immortalId = $immortalId;

        return $this;
    }

    /**
     * Get immortalId
     *
     * @return int
     */
    public function getImmortalId()
    {
        return $this->immortalId;
    }

    /**
     * Set anotherLogin
     *
     * @param string $anotherLogin
     *
     * @return User
     */
    public function setAnotherLogin($anotherLogin)
    {
        $this->anotherLogin = $anotherLogin;

        return $this;
    }

    /**
     * Get anotherLogin
     *
     * @return string
     */
    public function getAnotherLogin()
    {
        return $this->anotherLogin;
    }

    /**
     * Set lastMove
     *
     * @param \DateTime $lastMove
     *
     * @return User
     */
    public function setLastMove($lastMove)
    {
        $this->lastMove = $lastMove;

        return $this;
    }

    /**
     * Get lastMove
     *
     * @return \DateTime
     */
    public function getLastMove()
    {
        return $this->lastMove;
    }

    /**
     * Set balance
     *
     * @param integer $balance
     *
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $token
     * @return User
     */
    public function setToken($token) : User
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param mixed $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return boolean
     */
    public function getOffline() : boolean
    {
        return $this->offline;
    }

    /**
     * @param boolean $offline
     * @return User
     */
    public function setOffline(bool $offline)
    {
        $this->offline = $offline;

        return $this;
    }

    /**
     * @return PersistentCollection
     */
    public function getTournaments() : PersistentCollection
    {
        return $this->tournaments;
    }

    /**
     * @param PersistentCollection $tournaments
     * @return Tournament
     */
    public function setTournaments(PersistentCollection $tournaments) : Tournament
    {
        $this->tournaments = $tournaments;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return User
     */
    public function setSettings(array $settings) : User
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @param TournamentPlayer $tournament
     * @return User
     */
    public function addTournament(TournamentPlayer $tournament) : User
    {
        $this->tournaments->add($tournament);
    }

    /**
     * @param UserSetting $userSetting
     * @return User
     */
    public function setSetting(UserSetting $userSetting) : User
    {
        $this->settings[$userSetting->getName()] = $userSetting;
        return $this;
    }

    /**
     * @param TournamentPlayer $player
     * @return User
     */
    public function removeTournament(TournamentPlayer $player) : User
    {
        $this->tournaments->removeElement($player);

        return $this;
    }

    /**
     * @param string $name
     * @return UserSetting
     */
    public function getSetting(string $name) : UserSetting
    {
        if (!isset($this->settings[$name])) {
            throw new UserSettingNotFoundException;
        }
        
        return $this->settings[$name];
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    function __toString()
    {
        return $this->getLogin();
    }


}

