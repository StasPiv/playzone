<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 28.05.16
 * Time: 17:39
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunEventsCommand
 * @package CoreBundle\Command
 */
class CronCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:cron:run')
            ->setDescription('Run all current events from `events` table');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.handler.event")->runAllCurrentEvents();
        $this->getContainer()->get("core.handler.user")->markUsersOfflineWhoJustGone();
    }
}