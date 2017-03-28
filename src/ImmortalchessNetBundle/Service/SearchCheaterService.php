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

    /**
     * @param int $from
     * @param int $to
     */
    public function searchCheaterAndPublishPost(int $from = 0, int $to = 1)
    {
        $sql = sprintf(
            'SELECT
  game_id,
  cheater_table.login as suspected_in_cheating,
  opponent_table.login as opponent,
  IF(gm.user_id = id_white, g.result_white, g.result_black) as result,
  ROUND(100 * SUM(IF(delay BETWEEN 0 AND 3, 1, 0)) / COUNT(delay), 2) as firstInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 1 AND 4, 1, 0)) / COUNT(delay), 2) as secondInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 2 AND 5, 1, 0)) / COUNT(delay), 2) as thirdInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 3 AND 6, 1, 0)) / COUNT(delay), 2) as fourthInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 4 AND 7, 1, 0)) / COUNT(delay), 2) as fifthInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 5 AND 8, 1, 0)) / COUNT(delay), 2) as sixthInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 6 AND 9, 1, 0)) / COUNT(delay), 2) as seventhInterval,
  ROUND(100 * SUM(IF(delay BETWEEN 7 AND 10, 1, 0)) / COUNT(delay), 2) as eighthInterval,
  IF(gm.user_id = id_white, g.count_switching_white, g.count_switching_black) as switching
FROM game_move gm
  JOIN game g ON g.id = gm.game_id
  JOIN user opponent_table ON opponent_table.id = IF(gm.user_id = id_white, g.id_black, g.id_white)
  JOIN user cheater_table ON cheater_table.id = gm.user_id
WHERE delay > 0 AND gm.time_move BETWEEN (CURDATE() - INTERVAL %s DAY) AND (CURDATE() + INTERVAL %s DAY)
GROUP BY user_id, game_id
HAVING COUNT(delay) > 10
       AND (fourthInterval >= 60 OR fifthInterval >= 60 OR sixthInterval >= 60 OR seventhInterval >= 60 OR eighthInterval >= 60 OR firstInterval < 10 OR secondInterval < 10)
ORDER BY fourthInterval DESC',
            $from,
            $to
        );

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