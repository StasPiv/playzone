<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.02.16
 * Time: 15:45
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GamePutPgnRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePutPgnRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;
    
    /**
     * @var int
     *
     * @JMS\Type("string")
     */
    private $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $pgn;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeWhite;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $timeBlack;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $insufficientMaterialWhite = false;

    /**
     * @var boolean
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    private $insufficientMaterialBlack = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GamePutPgnRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgn()
    {
        return $this->pgn;
    }

    /**
     * @param string $pgn
     * @return GamePutPgnRequest
     */
    public function setPgn($pgn)
    {
        $this->pgn = $pgn;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeWhite()
    {
        return $this->timeWhite;
    }

    /**
     * @param int $timeWhite
     * @return GamePutPgnRequest
     */
    public function setTimeWhite($timeWhite)
    {
        $this->timeWhite = $timeWhite;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeBlack()
    {
        return $this->timeBlack;
    }

    /**
     * @param int $timeBlack
     * @return GamePutPgnRequest
     */
    public function setTimeBlack($timeBlack)
    {
        $this->timeBlack = $timeBlack;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInsufficientMaterialWhite() : bool
    {
        return $this->insufficientMaterialWhite;
    }

    /**
     * @param boolean $insufficientMaterialWhite
     * @return GamePutPgnRequest
     */
    public function setInsufficientMaterialWhite(bool $insufficientMaterialWhite)
    {
        $this->insufficientMaterialWhite = $insufficientMaterialWhite;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInsufficientMaterialBlack() : bool
    {
        return $this->insufficientMaterialBlack;
    }

    /**
     * @param boolean $insufficientMaterialBlack
     * @return GamePutPgnRequest
     */
    public function setInsufficientMaterialBlack(bool $insufficientMaterialBlack)
    {
        $this->insufficientMaterialBlack = $insufficientMaterialBlack;

        return $this;
    }
}