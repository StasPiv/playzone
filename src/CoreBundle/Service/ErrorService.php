<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 15:39
 */

namespace CoreBundle\Service;

use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\RequestInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ErrorService
{
    use ContainerAwareTrait;

    /**
     * UserHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param RequestErrorInterface $requestError
     * @param int $code
     * @throws ProcessorException
     */
    public function throwExceptionIfHasErrors(RequestErrorInterface $requestError, int $code)
    {
        $className = get_class($requestError);
        $initialState = new $className;

        if ($initialState != $requestError) {
            $requestError->throwException($code);
        }
    }
}