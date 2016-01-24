<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.01.16
 * Time: 22:48
 */

namespace WebsocketServerBundle\Service;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WebsocketServerBundle\Exception\PlayzoneServerException;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketServerBundle\Model\Message\Server\AskIntroduction;
use WebsocketServerBundle\Model\CallUser;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\Server\WelcomeMessage;

class PlayzoneServer implements MessageComponentInterface, ContainerAwareInterface
{
    /**
     * @var CallUser[]
     */
    private $users;

    /**
     * @var ContainerInterface
     */
    private $container;

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
     */
    public function __construct()
    {
        $this->users = new \SplObjectStorage;
    }


    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $callUser = new CallUser();
        $callUser->setConnection($conn);
        $this->users->attach($callUser);
        $this->send(new AskIntroduction(), $callUser->getConnection());
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
                    $this->sendToUsers($messageObject);
            }
        } catch (\Exception $exception) {
            $from->send($exception->getMessage());
        }
    }

    /**
     * @param ConnectionInterface $from
     * @param string $data json data
     */
    private function addUser(ConnectionInterface $from, $data)
    {
        $newCallUser = $this->getObjectFromJson(json_encode($data), 'WebsocketServerBundle\Model\CallUser');

        if (!$newCallUser instanceof CallUser) {
            throw new PlayzoneServerException("This is not CallUser instance in data");
        }

        if ($this->container->get('validator')->validate($newCallUser)->count() > 0) {
            throw new PlayzoneServerException("Validator found some errors");
        }

        $playzoneUser = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($newCallUser, clone $newCallUser);

        foreach ($this->users as $callUser) {
            if ($callUser->getConnection() == $from) {
                $callUser->setPlayzoneUser($playzoneUser);
                $this->send(new WelcomeMessage($callUser->getPlayzoneUser()->getLogin()), $from);
            }
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
     */
    private function sendToUsers(PlayzoneMessage $messageObject)
    {
        foreach ($this->users as $callUser) {
            if (in_array($callUser->getPlayzoneUser()->getLogin(), $messageObject->getLogins())) {
                $this->send($messageObject, $callUser->getConnection());
            }
        }
    }

    /**
     * @param PlayzoneMessage $messageObject
     * @param ConnectionInterface $connection
     */
    private function send(PlayzoneMessage $messageObject, ConnectionInterface $connection)
    {
        $connection->send(
            $this->container->get('jms_serializer')->serialize($messageObject, 'json')
        );
    }

    /**
     * @param string $jsonObject
     * @param string $fullClassName
     * @return CallUser|PlayzoneMessage
     */
    private function getObjectFromJson($jsonObject, $fullClassName)
    {
        return $this->container->get('jms_serializer')->deserialize($jsonObject, $fullClassName, 'json');
    }
}