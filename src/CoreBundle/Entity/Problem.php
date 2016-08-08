<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 0:08
 */

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Problem
 * @package CoreBundle\Entity
 *
 * @ORM\Entity
 */
class Problem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $fen;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $pgn;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFen() : string
    {
        return $this->fen;
    }

    /**
     * @param string $fen
     * @return Problem
     */
    public function setFen(string $fen) : self
    {
        $this->fen = $fen;

        return $this;
    }

    /**
     * @return string
     */
    public function getPgn() : string
    {
        return $this->pgn;
    }

    /**
     * @param string $pgn
     * @return Problem
     */
    public function setPgn(string $pgn) : self
    {
        $this->pgn = $pgn;

        return $this;
    }
}