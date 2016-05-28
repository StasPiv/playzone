<?php

namespace CoreBundle\Model\Request\Tournament;

use CoreBundle\Model\Request\SecurityRequestAwareTrait;
use CoreBundle\Model\Request\SecurityRequestInterface;
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
}