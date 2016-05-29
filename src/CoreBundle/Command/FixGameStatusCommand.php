<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 29.05.16
 * Time: 20:23
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateTournamentCommand
 * @package CoreBundle\Command
 */
class FixGameStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:fix:result')
             ->setDescription('Create tournament event')
             ->addArgument('gameId')
             ->addArgument('status')
             ->addArgument('resultWhite')
             ->addArgument('resultBlack');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.handler.game")
             ->changeGameStatusById(
                 $input->getArgument("gameId"), 
                 $input->getArgument("status"), 
                 $input->getArgument("resultWhite"),
                 $input->getArgument("resultBlack")
             );
    }
}