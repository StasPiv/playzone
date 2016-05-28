<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.01.16
 * Time: 20:21
 */

namespace CoreBundle\Model\Request\Game;

use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Call\CallType;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\User\UserType;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GameGetListRequest
 * @package CoreBundle\Model\Request\Game
 */
class GameGetListRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;    

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $user;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $status;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $callType;

    /**
     * @var integer
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $limit = 100;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCallType()
    {
        return $this->callType;
    }

    /**
     * @param mixed $callType
     */
    public function setCallType($callType)
    {
        $this->callType = $callType;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return GameGetListRequest
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
}