<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.01.16
 * Time: 15:52
 */

namespace WebsocketServerBundle\Model\Message\Server;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;

class AskIntroduction extends PlayzoneMessage
{
    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Type is required"
     * )
     */
    protected $scope = PlayzoneServerMessageScope::ASK_INTRODUCTION;

    /**
     * @var string
     *
     * @JMS\Expose()
     * @JMS\Type("string")
     *
     * @Assert\NotBlank(
     *     message = "Method is required"
     * )
     */
    protected $method = PlayzoneServerMessageScope::ASK_INTRODUCTION;
}