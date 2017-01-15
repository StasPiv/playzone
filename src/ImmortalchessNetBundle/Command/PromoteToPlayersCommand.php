<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 2:08
 */

namespace ImmortalchessNetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DatabaseConverterCommand
 * @package ImmortalchessNetBundle\Command
 */
class PromoteToPlayersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('immortalchessnet:promote_to_players')
            ->setDescription('This command promotes to group players');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('immortalchessnet.service.promote')
            ->run(
                $this->getContainer()->get('immortalchessnet.service.promotion_registered_to_players.rule'),
                $output
            );
    }
}