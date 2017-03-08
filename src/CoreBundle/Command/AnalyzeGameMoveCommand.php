<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 08.03.17
 * Time: 18:04
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AnalyzeGameMoveCommand
 * @package CoreBundle\Command
 */
class AnalyzeGameMoveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:analyze:game_move')
            ->setDescription('Analyze game move')
            ->addArgument('user', InputArgument::REQUIRED)
            ->addArgument('game', InputArgument::OPTIONAL);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getContainer()->get('doctrine')->getRepository('CoreBundle:User')->find($input->getArgument('user'));

        if ($input->getArgument('game')) {
            $game = $this->getContainer()->get('doctrine')->getRepository('CoreBundle:Game')->find($input->getArgument('game'));
        } else {
            $game = null;
        }

        $stat = $this->getContainer()->get('core.user.stat')->analyzeGameMove($user, $game);

        print_r($stat);

        $output->writeln($stat);
    }
}