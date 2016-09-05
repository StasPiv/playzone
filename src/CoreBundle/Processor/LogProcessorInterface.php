<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 13.06.16
 * Time: 13:03
 */

namespace CoreBundle\Processor;

use CoreBundle\Entity\Log;
use CoreBundle\Model\Request\Log\LogPostRequest;

/**
 * Interface LogProcessorInterface
 * @package CoreBundle\Processor
 */
interface LogProcessorInterface extends ProcessorInterface
{
    /**
     * @param LogPostRequest $request
     * @return mixed
     */
    public function processPost(LogPostRequest $request) : Log;
}