<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 15.01.17
 * Time: 14:38
 */

namespace ImmortalchessNetBundle\Service\Promotion;


use CoreBundle\Model\Event\EventCommandInterface;
use CoreBundle\Model\Event\EventInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PromotionToPlayersEventCommand
 * @package ImmortalchessNetBundle\Service\Promotion
 */
class PromotionToPlayersEventCommand implements ContainerAwareInterface, EventCommandInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->container->get('immortalchessnet.service.promote')->run(
            $this->container->get('immortalchessnet.service.promotion_registered_to_players.rule'),
            new NullOutput()
        );
    }

    /**
     * @inheritDoc
     */
    public function setEventModel(EventInterface $eventModel)
    {
        // TODO: Implement setEventModel() method.
    }


}