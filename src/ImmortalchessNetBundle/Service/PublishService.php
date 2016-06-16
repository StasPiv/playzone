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
     * @param PostModel $post
     * @throws \Doctrine\DBAL\DBALException
     */
    public function publishNewPost(PostModel $post)
    {
        $this->getConnection()->exec(
            "DELETE FROM post WHERE ipaddress = '' AND threadid = '{$post->getThreadId()}'"
        );

        $this->getConnection()->exec(
            "
                INSERT INTO post 
                (threadid, parentid, username, userid, title, pagetext, visible, dateline)
                VALUE
                ({$post->getThreadId()}, {$post->getFirstThreadPostId()}, '{$post->getLastPosterName()}', 
                {$post->getLastPosterId()}, '{$post->getTitle()}', 
                '{$post->getPageText()}', 1, 
                UNIX_TIMESTAMP(CURRENT_TIMESTAMP())
                );    
            "
        );

        $newPostId = $this->getConnection()->lastInsertId();

        $this->getConnection()->exec(
            "
            UPDATE thread SET lastpostid = '$newPostId', lastpost = UNIX_TIMESTAMP(CURRENT_TIMESTAMP()), 
            lastposter = '{$post->getLastPosterName()}', title = '{$post->getTitle()}'
            WHERE threadid = '{$post->getThreadId()}'
        "
        );

        $this->getConnection()->exec("
            UPDATE forum SET lastpostid = '$newPostId', lastpost = UNIX_TIMESTAMP(CURRENT_TIMESTAMP()),
            lastposter = '{$post->getLastPosterName()}', lastthreadid = '{$post->getThreadId()}', lastthread = 
            '{$post->getTitle()}'
            WHERE forumid = '{$post->getForumId()}'
        ");
    }

    /**
     * @param PostModel $postModel
     * @throws \Doctrine\DBAL\DBALException
     */
    public function publishNewPostNew(PostModel $postModel)
    {
        $this->getConnection()->exec(
            "DELETE FROM post WHERE ipaddress = '' AND threadid = '{$postModel->getThreadId()}'"
        );

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
               ->setTitle($post->getTitle())
               ->setThreadid($post->getThreadid());
        
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