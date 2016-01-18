<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 17.01.16
 * Time: 21:02
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\User;
use CoreBundle\Model\Request\SecurityRequestInterface;
use CoreBundle\Model\Response\ResponseStatusCode;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class SecurityHandler
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
     * @param SecurityRequestInterface $securityError
     * @return User
     */
    public function getMeIfCredentialsIsOk(
        SecurityRequestInterface $securityRequest,
        SecurityRequestInterface $securityError
    ) {
        $me = $this->container->get("core.handler.user")->getUserByLoginAndToken(
            $securityRequest->getLogin(),
            $securityRequest->getToken()
        );

        if (!$me instanceof User) {
            $securityError->setLogin("Forbidden for user with this credentials");
            $securityError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        return $me;
    }
}