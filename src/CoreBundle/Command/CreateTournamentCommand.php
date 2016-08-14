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
class CreateTournamentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:tournament:event')
             ->setDescription('Create tournament event')
             ->addArgument('frequency')
             ->addArgument('timeBegin')
             ->addArgument('tournamentName')
             ->addArgument('timeBase')
             ->addArgument('timeIncrement')
             ->addArgument('private', null, '', 0)
             ->addArgument('players', null, '', '')
             ->addArgument('gamesVsOpponent', null, '', 1);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.handler.tournament")
             ->createTournamentEvent(
                 $input->getArgument('frequency'),
                 $input->getArgument('timeBegin'),
                 $input->getArgument('tournamentName'),
                 $input->getArgument('timeBase'),
                 $input->getArgument('timeIncrement'),
                 $input->getArgument('private'),
                 $input->getArgument('players'),
                 $input->getArgument('gamesVsOpponent')
             );
    }
}