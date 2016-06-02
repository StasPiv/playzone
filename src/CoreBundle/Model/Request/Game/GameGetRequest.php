<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 12.02.16
 * Time: 21:13
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GameGetRequest
 * @package CoreBundle\Model\Request\Game
 */
class GameGetRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var int
     *
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $login;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $token;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GameGetRequest
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}