<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 08.03.17
 * Time: 12:30
 */

namespace CoreBundle\Model\Request\Game;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;

/**
 * Class GamePutCountEventsRequest
 * @package CoreBundle\Model\Request\Game
 */
class GamePutCountEventsRequest extends GameRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

    /**
     * @var int
     *
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $countSwitching = 0;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $countMouseLeave = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GamePutCountEventsRequest
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountSwitching(): int
    {
        return $this->countSwitching;
    }

    /**
     * @param int $countSwitching
     * @return GamePutCountEventsRequest
     */
    public function setCountSwitching(int $countSwitching): self
    {
        $this->countSwitching = $countSwitching;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountMouseLeave(): int
    {
        return $this->countMouseLeave;
    }

    /**
     * @param int $countMouseLeave
     * @return GamePutCountEventsRequest
     */
    public function setCountMouseLeave(int $countMouseLeave): self
    {
        $this->countMouseLeave = $countMouseLeave;

        return $this;
    }
}