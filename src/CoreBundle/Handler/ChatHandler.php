<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 03.05.16
 * Time: 18:07
 */

namespace CoreBundle\Handler;

use CoreBundle\Entity\ChatMessage;
use CoreBundle\Model\ChatMessage\ChatMessageType;
use CoreBundle\Model\Request\Call\ErrorAwareTrait;
use CoreBundle\Model\Request\Chat\ChatGetMessagesRequest;
use CoreBundle\Model\Request\Chat\ChatPostMessageRequest;
use CoreBundle\Model\Response\ResponseStatusCode;
use CoreBundle\Processor\ChatProcessorInterface;
use CoreBundle\Repository\ChatMessageRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ChatHandler
 * @package CoreBundle\Handler
 */
class ChatHandler implements ChatProcessorInterface
{
    use ContainerAwareTrait;
    use ErrorAwareTrait;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ChatMessageRepository
     */
    private $repository;

    /**
     * ChatHandler constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->repository = $this->manager->getRepository('CoreBundle:ChatMessage');
    }

    /**
     * @param ChatPostMessageRequest $request
     * @return mixed
     */
    public function processPostMessage(ChatPostMessageRequest $request) : ChatMessage
    {
        $me = $this->container->get("core.service.security")->getUserIfCredentialsIsOk($request, $this->getRequestError());
        
        if ($me->isBanned()) {
            $this->getRequestError()->addError("user", "You are banned")
                                    ->throwException(ResponseStatusCode::FORBIDDEN);
        }

        $chatMessage = new ChatMessage();

        $chatMessage->setType(ChatMessageType::COMMON())
                    ->setMessage($request->getMessage())
                    ->setUser($me);

        $this->manager->persist($chatMessage);
        
        $this->manager->flush();

        return $chatMessage;
    }

    /**
     * @param ChatGetMessagesRequest $request
     * @return ChatMessage[]
     */
    public function processGetMessages(ChatGetMessagesRequest $request) : array
    {
        return $this->repository->createQueryBuilder("cm")
                    ->select(["u.id", "u.login", "cm.time", "cm.message"])
                    ->where("cm.type = :type")
                    ->setParameter("type", ChatMessageType::COMMON)
                    ->innerJoin("cm.user", "u")
                    ->orderBy("cm.id", "DESC")
                    ->setMaxResults($this->container->getParameter("app_last_chat_messages_count"))
                    ->getQuery()
                    ->getResult();
    }
}