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
class GamePostPublishRequest extends GameRequest implements SecurityRequestInterface
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
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $fen;

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
     * @return GamePostPublishRequest
     */
    public function setPgn($pgn)
    {
        $this->pgn = $pgn;

        return $this;
    }

    /**
     * @return string
     */
    public function getFen()
    {
        return $this->fen;
    }

    /**
     * @param string $fen
     * @return GamePostPublishRequest
     */
    public function setFen($fen)
    {
        $this->fen = $fen;

        return $this;
    }
}