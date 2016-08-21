<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 16.06.16
 * Time: 0:22
 */

namespace ImmortalchessNetBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
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
            ->setParentid(0)
            ->setUsername($postModel->getLastPosterName())
            ->setUserid($postModel->getLastPosterId())
            ->setTitle($postModel->getTitle())
            ->setPagetext($postModel->getPageText())
            ->setVisible(1)
            ->setDateline(time());

        $this->getManager()->persist($post);

        try {
            $this->getManager()->flush();
        } catch (\Exception $e) {
            $this->container->get("logger")->error(__METHOD__ . " " . $e->getMessage());
        }

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
        
        try {
            $this->getManager()->flush();
        } catch (\Exception $e) {
            $this->container->get("logger")->error(__METHOD__ . " " . $e->getMessage());
        }
    }

    /**
     * @param PostModel $postModel
     * @throws \Doctrine\DBAL\DBALException
     */
    public function publishNewThread(PostModel $postModel)
    {
        $thread = (new Thread())->setForumid($postModel->getForumId())
                                ->setTitle($postModel->getTitle())
                                ->setOpen(1)
                                ->setVisible(1)
                                ->setTaglist($postModel->getTaglist());

        $this->getManager()->persist($thread);
        $this->getManager()->flush();
        
        $post = (new Post())->setThreadid($thread->getThreadid())
            ->setParentid(0)
            ->setUsername($postModel->getLastPosterName())
            ->setUserid($postModel->getLastPosterId())
            ->setTitle($postModel->getTitle())
            ->setPagetext($postModel->getPageText())
            ->setVisible(1)
            ->setDateline(time());

        $this->getManager()->persist($post);

        try {
            $this->getManager()->flush();
        } catch (\Exception $e) {
            $this->container->get("logger")->error(__METHOD__ . " " . $e->getMessage());
        }

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
        
        try {
            $this->getManager()->flush();
        } catch (\Exception $e) {
            $this->container->get("logger")->error(__METHOD__ . " " . $e->getMessage());
        }
    }

    /**
     * @return ObjectManager
     */
    private function getManager() : ObjectManager
    {
        return $this->container->get('doctrine')->getManager('immortalchess');
    }

    /**
     * @param string $text
     * @return string
     *
     * @deprecated use TextConvertor instead
     */
    public function convertText(string $text)
    {
        return str_replace(
            ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'],
            ['Ð','Ð‘','Ð’','Ð“','Ð”','Ð•','Ð','Ð–','Ð—','Ð˜','Ð™','Ðš','Ð›','Ðœ','Ð','Ðž','ÐŸ','Ð ','Ð¡','Ð¢','Ð£','Ð¤','Ð¥','Ð¦','Ð§','Ð¨','Ð©','Ðª','Ð«','Ð¬','Ð­','Ð®','Ð¯','Ð°','Ð±','Ð²','Ð³','Ð´','Ðµ','Ñ‘','Ð¶','Ð·','Ð¸','Ð¹','Ðº','Ð»','Ð¼','Ð½','Ð¾','Ð¿','Ñ€','Ñ','Ñ‚','Ñƒ','Ñ„','Ñ…','Ñ†','Ñ‡','Ñˆ','Ñ‰','ÑŠ','Ñ‹','ÑŒ','Ñ','ÑŽ','Ñ'],
            $text
        );
    }

    /**
     * @param string $text
     * @return string
     *
     * @deprecated use TextConvertor instead
     */
    public function convertTextOpposite(string $text)
    {
        return str_replace(
            ['Ð','Ð‘','Ð’','Ð“','Ð”','Ð•','Ð','Ð–','Ð—','Ð˜','Ð™','Ðš','Ð›','Ðœ','Ð','Ðž','ÐŸ','Ð ','Ð¡','Ð¢','Ð£','Ð¤','Ð¥','Ð¦','Ð§','Ð¨','Ð©','Ðª','Ð«','Ð¬','Ð­','Ð®','Ð¯','Ð°','Ð±','Ð²','Ð³','Ð´','Ðµ','Ñ‘','Ð¶','Ð·','Ð¸','Ð¹','Ðº','Ð»','Ð¼','Ð½','Ð¾','Ð¿','Ñ€','Ñ','Ñ‚','Ñƒ','Ñ„','Ñ…','Ñ†','Ñ‡','Ñˆ','Ñ‰','ÑŠ','Ñ‹','ÑŒ','Ñ','ÑŽ','Ñ'],
            ['А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'],
            $text
        );
    }
}