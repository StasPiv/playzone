<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.01.16
 * Time: 22:48
 */

namespace WebsocketServerBundle\Service;

use CoreBundle\Entity\ChatMessage;
use CoreBundle\Entity\User;
use CoreBundle\Model\Event\User\UserEvent;
use CoreBundle\Model\Event\User\UserEvents;
use CoreBundle\Model\Request\RequestErrorInterface;
use Monolog\Logger;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WebsocketServerBundle\Exception\PlayzoneServerException;
use WebsocketServerBundle\Model\Message\Client\Game\ClientMessageGameSend;
use WebsocketServerBundle\Model\Message\Client\Game\ClientMessageGameSubscribe;
use WebsocketServerBundle\Model\Message\Client\Game\ClientMessageMessageSend;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketServerBundle\Model\Message\Server\AskIntroduction;
use WebsocketServerBundle\Model\Message\Server\Game\ServerGameSendMove;
use WebsocketServerBundle\Model\Message\Server\PlayzoneServerMessageScope;
use WebsocketServerBundle\Model\WebsocketUser;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\Server\WelcomeMessage;

/**
 * Class PlayzoneServer
 * @package WebsocketServerBundle\Service
 */
class PlayzoneServer implements MessageComponentInterface, ContainerAwareInterface
{
    /**
     * @var WebsocketUser[]
     */
    private $users;

    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var Logger */
    private $logger;

    /** @var array */
    private $playzoneUsersMap = [];

    /**
     * PHP 5 allows developers to declare constructor methods for classes.
     * Classes which have a constructor method call this method on each newly-created object,
     * so it is suitable for any initialization that the object may need before it is used.
     *
     * Note: Parent constructors are not called implicitly if the child class defines a constructor.
     * In order to run a parent constructor, a call to parent::__construct() within the child constructor is required.
     *
     * param [ mixed $args [, $... ]]
     * @link http://php.net/manual/en/language.oop5.decon.php
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->users = new \SplObjectStorage;
    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $wsUser = new WebsocketUser();
        $wsUser->setConnection($conn);
        $this->users->attach($wsUser);
        $this->send(new AskIntroduction(), $wsUser);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->users as $user) {
            if ($user->getConnection() == $conn) {
                $this->users->detach($user);

                if (!$user->getPlayzoneUser()) {
                    continue;
                }

                $countIn = $this->updatePlayzoneUsersMap($user->getPlayzoneUser(), -1);

                if ($countIn == 0) {
                    $this->container->get("event_dispatcher")->dispatch(
                        UserEvents::USER_OUT,
                        (new UserEvent())->setUser($user->getPlayzoneUser())
                    );

                    $this->sendToUsers(
                        (new PlayzoneMessage())->setScope(PlayzoneServerMessageScope::USER_GONE)
                            ->setMethod(PlayzoneClientMessageMethod::USER_GONE)
                            ->setData([
                                'id' => $user->getPlayzoneUser()->getId(),
                                'login' => $user->getPlayzoneUser()->getLogin()
                            ])
                    );
                }

            }
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->err("Error and close: " . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $messageObject = $this->getMessageObject($msg);

            if ($this->container->get('validator')->validate($messageObject)->count() > 0) {
                throw new PlayzoneServerException("Validator found some errors");
            }

            switch ($messageObject->getScope()) {
                case PlayzoneClientMessageScope::INTRODUCTION:
                    $this->addUser($from, $messageObject->getData());
                    break;
                case PlayzoneClientMessageScope::SEND_TO_USERS:
                    $this->sendToUsers($messageObject, $from);
                    break;
                case PlayzoneClientMessageScope::SEND_TO_GAME_OBSERVERS:
                    $this->sendToGameObservers($messageObject, $from);
                    break;
                case PlayzoneClientMessageScope::SUBSCRIBE_TO_GAME:
                    $this->addGameForListen($messageObject, $from);
                    break;
                case PlayzoneClientMessageScope::STOP_SERVER:
                    die('SERVER STOPPED');
            }
        } catch (\Exception $exception) {
            $this->logger->err("Error on message: " . $exception->getCode() . " " . $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
            $from->send($exception->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param string $data json data
     */
    private function addUser(ConnectionInterface $from, $data)
    {
        $newWsUser = $this->getObjectFromJson(json_encode($data), 'WebsocketServerBundle\Model\WebsocketUser');

        if (!$newWsUser instanceof WebsocketUser) {
            throw new PlayzoneServerException("This is not WebsocketUser instance in data");
        }

        if ($this->container->get('validator')->validate($newWsUser)->count() > 0) {
            throw new PlayzoneServerException("Validator found some errors");
        }

        $playzoneUser = $this->container->get("core.service.security")
                             ->getUserIfCredentialsIsOk(
                                 $newWsUser,
                                 $this->container->get("core.request.error")
                             );

        foreach ($this->users as $wsUser) {
            if ($wsUser->getConnection() != $from) {
                continue;
            }

            $wsUser->setPlayzoneUser($playzoneUser);

            $countIn = $this->updatePlayzoneUsersMap($playzoneUser, 1);

            if ($countIn == 1) {
                $this->container->get("event_dispatcher")->dispatch(
                    UserEvents::USER_IN,
                    (new UserEvent())->setUser($playzoneUser)
                );

                $this->sendToUsers(
                    (new PlayzoneMessage())
                        ->setScope(PlayzoneServerMessageScope::USER_IN)
                        ->setMethod(PlayzoneClientMessageMethod::USER_IN)
                        ->setData(
                            json_decode(
                                $this->container->get('serializer')->serialize(
                                    $wsUser->getPlayzoneUser(),
                                    'json'
                                ),
                                true
                            )
                        )
                );
            }

            $this->sendWelcomeMessage($wsUser);
        }
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     * @return $this
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param string $msg
     * @return PlayzoneMessage|null
     */
    private function getMessageObject($msg)
    {
        $messageObject = $this->getObjectFromJson($msg, 'WebsocketServerBundle\Model\Message\PlayzoneMessage');

        if (!$messageObject instanceof PlayzoneMessage) {
            return null;
        }

        if ($this->container->get("validator")->validate($messageObject)->count() > 0) {
            return null;
        }

        return $messageObject;
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $from
     */
    private function sendToUsers(PlayzoneMessage $messageObject, ConnectionInterface $from = null)
    {
        if ($from &&
            strpos($messageObject->getMethod(), PlayzoneClientMessageMethod::SEND_MESSAGE_TO_OBSERVERS) === 0) {
            $this->sendMessageToGameObservers($messageObject, $from);
            return;
        }

        foreach ($this->users as $wsUser) {
            if (!$wsUser->getPlayzoneUser() instanceof User) {
                continue;
            }

            if (empty($messageObject->getLogins()) || in_array($wsUser->getPlayzoneUser()->getLogin(),
                $messageObject->getLogins())) {
                $this->send($messageObject, $wsUser);
            }
        }
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $from
     */
    private function sendToGameObservers(PlayzoneMessage $messageObject, ConnectionInterface $from)
    {
        switch ($messageObject->getMethod()) {
            case PlayzoneClientMessageMethod::SEND_PGN_TO_OBSERVERS:
                $this->sendGameToGameObservers($messageObject, $from);
                break;
        }
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $from
     */
    private function sendGameToGameObservers(PlayzoneMessage $messageObject, ConnectionInterface $from)
    {
        /** @var ClientMessageGameSend $gameSendMessage */
        $gameSendMessage = $this->getObjectFromJson(json_encode($messageObject->getData()),
            'WebsocketServerBundle\Model\Message\Client\Game\ClientMessageGameSend');

        foreach ($this->users as $wsUser) {
            if ($wsUser->getConnection() != $from && isset($wsUser->getGamesToListenMap()[$gameSendMessage->getGameId()])) {
                $messageObject->setMethod("game_pgn_" . $gameSendMessage->getGameId())
                              ->setMs(microtime(true) * 10000);

                $this->send($messageObject, $wsUser);
            }
        }
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $from
     */
    private function sendMessageToGameObservers(PlayzoneMessage $messageObject, ConnectionInterface $from)
    {
        /** @var ClientMessageMessageSend $messageGame */
        $messageGame = $this->getObjectFromJson(json_encode($messageObject->getData()),
            'WebsocketServerBundle\Model\Message\Client\Game\ClientMessageMessageSend');

        foreach ($this->users as $wsUser) {
            if ($wsUser->getConnection() == $from) {
                $chatMessage = $this->container->get("core.handler.chat")->createEntity();

                $chatMessage->setUser($wsUser->getPlayzoneUser())
                            ->setMessage($messageGame->getMessage())
                            ->setTime($this->container->get("core.service.date")->getDateTime());

                $messageObject->setData(
                    json_decode(
                        $this->container->get("jms_serializer")->serialize($chatMessage, 'json'),
                        true
                    )
                );

                $this->send($messageObject, $wsUser);
            }
        }

        foreach ($this->users as $wsUser) {
            if ($wsUser->getConnection() != $from) {
                $messageObject->setMethod(
                    "send_message_to_observers_" . $messageGame->getGameId()
                );

                $this->send($messageObject, $wsUser);
            }
        }
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param WebsocketUser $wsUser
     */
    private function send(PlayzoneMessage $messageObject, WebsocketUser $wsUser)
    {
        try {
            $this->container->get("ws.handler.client.message")->prepareMessageForUser($messageObject, $wsUser);
            $message = $this->container->get('jms_serializer')->serialize($messageObject, 'json');
            $this->logger->info("Server: " . $message);
            $wsUser->getConnection()->send($message);
        } catch (\Exception $exception) {
            $this->logger->err("Error on send: " . $exception->getMessage() . ' ' . $exception->getFile() . ' ' . 
                $exception->getLine());
            $wsUser->getConnection()->send($exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine());
        }
    }

    /**
     * @param string $jsonObject
     * @param string $fullClassName
     * @return WebsocketUser|PlayzoneMessage
     */
    private function getObjectFromJson($jsonObject, $fullClassName)
    {
        return $this->container->get('jms_serializer')->deserialize($jsonObject, $fullClassName, 'json');
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $from
     */
    private function addGameForListen(PlayzoneMessage $messageObject, ConnectionInterface $from)
    {
        /** @var ClientMessageGameSubscribe $gameSubscribeMessage */
        $gameSubscribeMessage = $this->getObjectFromJson(json_encode($messageObject->getData()),
            'WebsocketServerBundle\Model\Message\Client\Game\ClientMessageGameSubscribe');

        foreach ($this->users as $wsUser) {
            if ($wsUser->getConnection() == $from) {
                $wsUser->addGameToListen($gameSubscribeMessage->getGameId());
            }
        }
    }

    /**
     * @param WebsocketUser $wsUser
     */
    private function sendWelcomeMessage(WebsocketUser $wsUser)
    {
        $anotherLogins = [];

        foreach ($this->users as $anotherUser) {
            if ($anotherUser->getPlayzoneUser() instanceof User) {
                $anotherLogins[] = $anotherUser->getPlayzoneUser();
            }
        }

        $this->send(
            new WelcomeMessage(
                $wsUser->getPlayzoneUser()->getLogin(),
                $anotherLogins
            ),
            $wsUser
        );
    }

    /**
     * @param User $playzoneUser
     * @param int $count
     * @return int
     */
    private function updatePlayzoneUsersMap(User $playzoneUser, int $count)
    {
        if (!isset($this->playzoneUsersMap[$playzoneUser->getId()])) {
            return $this->playzoneUsersMap[$playzoneUser->getId()] = 1;
        }

        return $this->playzoneUsersMap[$playzoneUser->getId()] += $count;
    }

    /**
     * @inheritDoc
     */
    function __destruct()
    {
        $this->container->get('logger')->debug('WEBSOCKET STOP');
        $this->container->get('core.handler.user')->markAllUsersOffline();
    }
}