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
 * Class GamePostAddmessageRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePostAddmessageRequest extends GameRequest implements SecurityRequestInterface
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
     *
     * @Assert\NotBlank(
     *     message = "Message is required for this request"
     * )
     */
    private $message;

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
    public function getMessage() : string 
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return GamePostAddmessageRequest
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }
}