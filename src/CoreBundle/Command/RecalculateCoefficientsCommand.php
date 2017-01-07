<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.06.16
 * Time: 23:59
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RecalculateCoefficientsCommand
 * @package CoreBundle\Command
 */
class RecalculateCoefficientsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:tournament:recalculate')
             ->addArgument('tournament')
             ->setDescription('Run all current events from `events` table');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.handler.tournament")
            ->recalculatePointsAndCoefficientsById($input->getArgument("tournament"));
    }
}