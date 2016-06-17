<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 29.05.16
 * Time: 20:23
 */

namespace CoreBundle\Command;

use CoreBundle\Exception\Handler\Tournament\TournamentNotFoundException;
use CoreBundle\Model\Event\Tournament\TournamentContainer;
use CoreBundle\Model\Event\Tournament\TournamentEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FinishTournamentCommand
 * @package CoreBundle\Command
 */
class FinishTournamentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:tournament:finish')
             ->setDescription('Finish tournament event')
             ->addArgument('tournamentId');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $tournament = $this->getContainer()->get("core.handler.tournament")->getRepository()->find(
                $input->getArgument('tournamentId')
            );
        } catch (TournamentNotFoundException $e) {
            $this->getContainer()->get("logger")->error("Tournament " . $input->getArgument("tournamentId") . " is not found");
            return;
        }

        $this->getContainer()->get("event_dispatcher")
             ->dispatch(
                 TournamentEvents::TOURNAMENT_FINISHED,
                 (new TournamentContainer())->setTournament($tournament)
             );
    }
}