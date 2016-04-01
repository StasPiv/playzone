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
     * @param RequestErrorInterface $RequestErrorInterface
     * @param int $code
     * @throws ProcessorException
     */
    public function throwExceptionIfHasErrors(RequestErrorInterface $RequestErrorInterface, $code)
    {
        $className = get_class($RequestErrorInterface);
        $initialState = new $className;

        if ($initialState != $RequestErrorInterface) {
            $RequestErrorInterface->throwException($code);
        }
    }
}