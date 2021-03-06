<?php

namespace CoreBundle\Model\Request\Tournament;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\Tournament\TournamentStatus;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.04.16
 * Time: 11:52
 */
class TournamentGetListRequest extends TournamentRequest implements SecurityRequestInterface
{
    use SecurityRequestAwareTrait;

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
     * @var TournamentStatus
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    private $status = TournamentStatus::NEW;

    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    private $limit = 10;

    /**
     * @return TournamentStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param TournamentStatus $status
     * @return TournamentGetListRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     * @return TournamentGetListRequest
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
}