<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.16
 * Time: 22:49
 */

namespace ImmortalchessNetBundle\Service;

use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\User\PasswordNotCorrectException;
use CoreBundle\Exception\Handler\User\TokenNotCorrectException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\Event\Call\CallEvent;
use CoreBundle\Model\Event\Call\CallEvents;
use CoreBundle\Model\Event\User\UserAuthEvent;
use CoreBundle\Model\Event\User\UserEvents;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use Doctrine\DBAL\Connection;
use ImmortalchessNetBundle\Entity\ImmortalUser;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ImmortalchessnetService
 * @package CoreBundle\Service
 */
class ImmortalchessnetService implements EventSubscriberInterface
{
    use ContainerAwareTrait;

    /**
     * @param string $loginOrEmail
     * @param string $password
     * @return User
     * @throws UserNotFoundException
     * @throws PasswordNotCorrectException
     */
    public function getUser(string $loginOrEmail, string $password) : User
    {
        try {
            $immortalUser = $this->getRepository()->findOneByUsername($loginOrEmail);
        } catch (UserNotFoundException $e) {
            $immortalUser = $this->getRepository()->findOneByEmail($loginOrEmail);
        }

        if (!$this->checkPassword($immortalUser, $password)) {
            throw new PasswordNotCorrectException;
        }

        return (new User())->setLogin($immortalUser->getUsername())
                           ->setEmail($immortalUser->getEmail());
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
        $firstPostForCalls = $this->container->getParameter("app_immortalchess.first_post_for_calls");
        $forumPlayzone = $this->container->getParameter("app_immortalchess.forum_playzone");
        $userIdForSent = $this->container->getParameter("app_immortalchess.post_userid_for_calls");

        $this->getConnection()->exec(
            "DELETE FROM post WHERE ipaddress = '' AND threadid = '$threadForCalls'"
        );

        $this->getConnection()->exec(
            "
                INSERT INTO post 
                (threadid, parentid, username, userid, title, pagetext, visible, dateline)
                VALUE
                ($threadForCalls, $firstPostForCalls, '{$call->getFromUser()}', $userIdForSent, '$title', '$pageText', 1, 
                UNIX_TIMESTAMP(CURRENT_TIMESTAMP())
                );    
            "
        );
        
        $newPostId = $this->getConnection()->lastInsertId();
        
        $this->getConnection()->exec(
            "
            UPDATE thread SET lastpostid = '$newPostId', lastpost = UNIX_TIMESTAMP(CURRENT_TIMESTAMP()), 
            lastposter = '{$call->getFromUser()}', title = '$title'
            WHERE threadid = '{$threadForCalls}'
        "
        );

        $this->getConnection()->exec("
            UPDATE forum SET lastpostid = '$newPostId', lastpost = UNIX_TIMESTAMP(CURRENT_TIMESTAMP()),
            lastposter = '{$call->getFromUser()}', lastthreadid = '{$threadForCalls}'
            WHERE forumid = '$forumPlayzone'
        ");
    }

    /**
     * @return Connection
     */
    private function getConnection() : Connection
    {
        return $this->container->get('doctrine')->getConnection('immortalchess');
    }

    /**
     * @param ImmortalUser $immortalUser
     * @param string $password
     * @return bool
     */
    private function checkPassword(ImmortalUser $immortalUser, string $password) : bool
    {
        return $immortalUser->getPassword() === md5(md5($password) . $immortalUser->getSalt());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CallEvents::NEW_CALL => [
                ['onNewCall', 10]
            ],
            UserEvents::USER_AUTH => [
                ['onUserAuth', 10]
            ]
        ];
    }

    /**
     * @param CallEvent $event
     */
    public function onNewCall(CallEvent $event)
    {
        $this->publishPostAboutNewCall($event->getCall());
    }

    /**
     * @param UserAuthEvent $event
     */
    public function onUserAuth(UserAuthEvent $event)
    {
        $user = $this->getUser($event->getLogin(), $event->getPassword())
                     ->setPassword(
                        $this->container->get("core.handler.user")
                             ->generatePasswordHash($event->getPassword())
                     );

        $event->setUser($user);
    }

    /**
     * @return \ImmortalchessNetBundle\Repository\ImmortalUserRepository
     */
    private function getRepository()
    {
        return $this->container->get("doctrine")->getManager("immortalchess")
            ->getRepository("ImmortalchessNetBundle:ImmortalUser");
    }
}