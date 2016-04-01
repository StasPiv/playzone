<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.04.16
 * Time: 20:13
 */

namespace CoreBundle\Model\Request\Call;

use CoreBundle\Model\Request\RequestErrorInterface;

trait ErrorAwareTrait
{
    /** @var RequestErrorInterface */
    private $requestError;

    /**
     * @return RequestErrorInterface
     */
    public function getRequestError() : RequestErrorInterface
    {
        return $this->requestError;
    }

    /**
     * @param RequestErrorInterface $requestError
     * @return ErrorAwareTrait
     */
    public function setRequestError(RequestErrorInterface $requestError)
    {
        $this->requestError = $requestError;

        return $this;
    }
    
}