<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 21:02
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\User;
use CoreBundle\Model\Request\RequestError;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

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
     * @param RequestError $securityError
     * @return User
     */
    public function getUserIfCredentialsIsOk(
        SecurityRequestInterface $securityRequest,
        RequestError $securityError
    ) {
        $user = $this->container->get("core.handler.user")->getUserByLoginAndToken(
            $securityRequest->getLogin(),
            $securityRequest->getToken()
        );

        if (!$user instanceof User) {
            $securityError->addError("login", "Forbidden for user with this credentials");
            $securityError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        return $user;
    }
}