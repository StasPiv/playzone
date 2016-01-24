<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 15:39
 */

namespace CoreBundle\Service;

use CoreBundle\Exception\Processor\ProcessorException;
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
     * @param RequestInterface $errorObject
     * @param int $code
     * @throws ProcessorException
     */
    public function throwExceptionIfHasErrors($errorObject, $code)
    {
        $className = get_class($errorObject);
        $initialState = new $className;

        if ($initialState != $errorObject) {
            $errorObject->throwException($code);
        }
    }
}