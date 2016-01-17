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
     * @param SecurityRequestInterface $removeRequest
     * @param SecurityRequestInterface $removeError
     * @return User
     */
    public function getMeIfCredentialsIsOk(
        SecurityRequestInterface $removeRequest,
        SecurityRequestInterface $removeError
    ) {
        $me = $this->container->get("core.handler.user")->getUserByLoginAndToken(
            $removeRequest->getLogin(),
            $removeRequest->getToken()
        );

        if (!$me instanceof User) {
            $removeError->setLogin("Forbidden for user with this credentials");
            $removeError->throwException(ResponseStatusCode::FORBIDDEN);
        }

        return $me;
    }
}