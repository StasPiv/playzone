<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.03.16
 * Time: 20:43
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GamePutAcceptdrawRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePutAcceptdrawRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;
    
    /**
     * @var int
     *
     * @JMS\Type("string")
     */
    private $id;

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
}