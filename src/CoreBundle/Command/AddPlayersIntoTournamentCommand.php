<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.09.16
 * Time: 8:58
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddPlayersIntoTournamentCommand
 * @package CoreBundle\Command
 */
class AddPlayersIntoTournamentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:tournament:add_players')
            ->setDescription('Add players into tournament')
            ->addArgument('tournamentId')
            ->addArgument('playersIdsString');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('core.handler.tournament')->addPlayersIntoTournament(
            $input->getArgument('tournamentId'),
            $input->getArgument('playersIdsString')
        );
    }
}