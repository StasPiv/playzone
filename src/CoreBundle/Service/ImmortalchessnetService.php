<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.16
 * Time: 22:49
 */

namespace CoreBundle\Service;

use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\Game\GameColor;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ImmortalchessnetService
 * @package CoreBundle\Service
 */
class ImmortalchessnetService
{
    use ContainerAwareTrait;
    
    /**
     * @param string $login
     * @param string $password
     * @return User
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    public function getUser(string $login, string $password) : User
    {
        $userData = $this->getUserData($login);

        if (!$this->checkPassword($userData, $password)) {
            throw new PasswordNotCorrectException;
        }

        return (new User())->setLogin($userData['username'])
                           ->setEmail($userData['email']);
    }

    /**
     * @param GameCall $call
     */
    public function publishPostAboutNewCall(GameCall $call)
    {
        $title = 'New call from ' . $call->getFromUser();
        $pageText = $this->container->get("templating")->render(
            'Post/newcall.html.twig',
            [
                'user' => $call->getFromUser(),
                'time_minutes' => $call->getGameParams()->getTimeBase() / 60000,
                'color' => GameColor::getOppositeColor($call->getGameParams()->getColor())
            ]
        );

        $threadForCalls = $this->container->getParameter("app_immortalchess.thread_for_calls");
        $userNameForSent = $this->container->getParameter("app_immortalchess.post_username_for_calls");
        $userIdForSent = $this->container->getParameter("app_immortalchess.post_userid_for_calls");

        $query = "
            INSERT INTO immortalchess.post 
            (threadid, username, userid, title, pagetext, visible, dateline)
            VALUE
            ($threadForCalls, '$userNameForSent', $userIdForSent, '$title', '$pageText', 1, 
            UNIX_TIMESTAMP(CURRENT_TIMESTAMP())
            );    
        ";

        $this->getConnection()->exec($query);
    }

    /**
     * @param string $login
     * @return array with user data
     * @throws UserNotFoundException
     */
    private function getUserData(string $login) : array
    {
        $userData = $this->getConnection()->fetchAssoc(
            "SELECT * FROM `user` 
             WHERE `username` = '$login'"
        );

        if ($userData === false) {
            throw new UserNotFoundException;
        }

        return $userData;
    }

    /**
     * @return Connection
     */
    private function getConnection() : Connection
    {
        return $this->container->get('doctrine')->getConnection('immortalchess');
    }

    /**
     * @param array $userData
     * @param string $password
     * @return bool
     */
    private function checkPassword(array $userData, string $password) : bool
    {
        return $userData['password'] === md5(md5($password) . $userData['salt']);
    }
}