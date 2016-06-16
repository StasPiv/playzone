<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.06.16
 * Time: 0:22
 */

namespace ImmortalchessNetBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\Connection;
use ImmortalchessNetBundle\Entity\Thread;
use ImmortalchessNetBundle\Exception\Forum\ForumNotFoundException;
use ImmortalchessNetBundle\Exception\Thread\ThreadNotFoundException;
use ImmortalchessNetBundle\Model\Post as PostModel;
use ImmortalchessNetBundle\Entity\Post;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PublishService
 * @package ImmortalchessNetBundle\Service
 */
class PublishService
{
    use ContainerAwareTrait;

    /**
     * @param PostModel $postModel
     * @throws \Doctrine\DBAL\DBALException
     */
    public function publishNewPost(PostModel $postModel)
    {
        $post = (new Post())->setThreadid($postModel->getThreadId())
            ->setParentid($postModel->getFirstThreadPostId())
            ->setUsername($postModel->getLastPosterName())
            ->setUserid($postModel->getLastPosterId())
            ->setTitle($postModel->getTitle())
            ->setPagetext($postModel->getPageText())
            ->setVisible(1)
            ->setDateline(time());

        $this->getManager()->persist($post);
        $this->getManager()->flush();

        try {
            $thread = $this->getManager()->getRepository("ImmortalchessNetBundle:Thread")
                ->find($post->getThreadid());
        } catch (ThreadNotFoundException $e) {
            $this->container->get("logger")->error("Thread #" . $post->getThreadid() . " is not found");
            return;
        }

        $thread->setLastpostid($post->getPostid())
               ->setLastpost($post->getDateline())
               ->setLastposter($post->getUsername())
               ->setThreadid($post->getThreadid())
               ->setReplycount($thread->getReplycount() + 1);
        
        $this->getManager()->persist($thread);

        try {
            $forum = $this->getManager()->getRepository("ImmortalchessNetBundle:Forum")
                ->find($thread->getForumid());
        } catch (ForumNotFoundException $e) {
            $this->container->get("logger")->error("Forum #" . $thread->getForumid() . " is not found");
            return;
        }

        $forum->setLastpostid($post->getPostid())
              ->setLastpost($post->getDateline())
              ->setLastposter($post->getUsername())
              ->setLastthreadid($post->getThreadid())
              ->setLastthread($thread->getTitle());
        
        $this->getManager()->persist($forum);
        
        $this->getManager()->flush();
    }

    /**
     * @return Connection
     */
    private function getConnection() : Connection
    {
        return $this->container->get('doctrine')->getConnection('immortalchess');
    }

    /**
     * @return ObjectManager
     */
    private function getManager() : ObjectManager
    {
        return $this->container->get('doctrine')->getManager('immortalchess');
    }
}