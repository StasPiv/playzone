<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.17
 * Time: 23:35
 */

namespace ImmortalchessNetBundle\Service;


use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use ImmortalchessNetBundle\Model\Post;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class SearchCheaterService
 * @package ImmortalchessNetBundle\Service
 */
class SearchCheaterService implements EventCommandInterface
{
    use ContainerAwareTrait;

    public function searchCheaterAndPublishPost()
    {
        $sql = 'SELECT
  game_id,
  cheater_table.login as suspected_in_cheating,
  opponent_table.login as opponent,
  IF(gm.user_id = id_white, g.result_white, g.result_black) as result,
  ROUND(100 * SUM(IF(delay > 3, 1, 0)) / COUNT(delay), 2) as delayPercentMoreThan3,
  IF(gm.user_id = id_white, g.count_switching_white, g.count_switching_black) as switching
FROM game_move gm
  JOIN game g ON g.id = gm.game_id
  JOIN user opponent_table ON opponent_table.id = IF(gm.user_id = id_white, g.id_black, g.id_white)
  JOIN user cheater_table ON cheater_table.id = gm.user_id
WHERE delay > 0 AND IF(gm.user_id = id_white, g.result_white, g.result_black) <> 0 AND gm.time_move >= CURDATE()
GROUP BY user_id, game_id
HAVING COUNT(delay) > 10 AND (delayPercentMoreThan3 > 85 OR switching > 5)
ORDER BY game_id DESC';

        $doctrine = $this->container->get('doctrine');

        /** @var EntityManager $em */
        $em = $doctrine->getManager();

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $cheaters = $stmt->fetchAll();

        if (count($cheaters) == 0) {
            return;
        }

        $twig = $this->container->get('twig');

        $text = $twig->render(':Post:cheaters.html.twig', ['cheaters' => $cheaters]);

        $this->container->get("immortalchessnet.service.publish")->publishNewPost(
            new Post(
                145,
                33288,
                $this->container->getParameter("app_immortalchess.post_username_for_calls"),
                $this->container->getParameter("app_immortalchess.post_userid_for_calls"),
                "Подозреваемые в читерстве",
                $text
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->searchCheaterAndPublishPost();
    }

    /**
     * @inheritDoc
     */
    public function setEventModel(EventInterface $eventModel)
    {
        // TODO: Implement setEventModel() method.
    }
}