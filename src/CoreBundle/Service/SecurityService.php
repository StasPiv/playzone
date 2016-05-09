<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 21:02
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserHandlerException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Request\RequestErrorInterface;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SecurityService
 * @package CoreBundle\Service
 */
class SecurityService
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
     * @param SecurityRequestInterface $securityRequest
     * @param RequestErrorInterface $securityError
     * @return User
     * @throws ProcessorException
     */
    public function getUserIfCredentialsIsOk(
        SecurityRequestInterface $securityRequest,
        RequestErrorInterface $securityError) : User
    {
        try {
            return $this->container->get("core.handler.user")->getUserByLoginAndToken(
                $securityRequest->getLogin(),
                $securityRequest->getToken()
            );
        } catch (UserNotFoundException $e) {
            $securityError->addError("login", "Login is not found")
                          ->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (TokenNotCorrectException $e) {
            $securityError->addError("token", "Forbidden for user with this credentials")
                          ->throwException(ResponseStatusCode::FORBIDDEN);
        } catch (FatalThrowableError $e) {
            $securityError->addError("token", "Forbidden for user with this credentials")
                ->throwException(ResponseStatusCode::FORBIDDEN);
        }
    }
}