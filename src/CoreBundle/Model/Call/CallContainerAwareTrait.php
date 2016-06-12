<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 11.06.16
 * Time: 20:08
 */

namespace CoreBundle\Model\Call;

use CoreBundle\Entity\GameCall;

/**
 * Class CallContainerAwareTrait
 * @package CoreBundle\Model\Call
 */
trait CallContainerAwareTrait
{
    /**
     * @var GameCall
     */
    protected $call;

    /**
     * @return GameCall
     */
    public function getCall() : GameCall
    {
        return $this->call;
    }

    /**
     * @param GameCall $call
     * @return CallContainerAwareTrait
     */
    public function setCall(GameCall $call)
    {
        $this->call = $call;

        return $this;
    }
}