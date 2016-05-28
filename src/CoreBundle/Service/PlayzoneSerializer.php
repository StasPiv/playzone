<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 11:44
 */

namespace CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PlayzoneSerializer
 * @package CoreBundle\Service
 */
class PlayzoneSerializer
{
    use ContainerAwareTrait;

    /**
     * @param $object
     * @return array
     */
    public function toArray($object) : array 
    {
        return json_decode(
            $this->container->get("serializer")->serialize(
                $object,
                "json"
            ), true
        );
    }
}