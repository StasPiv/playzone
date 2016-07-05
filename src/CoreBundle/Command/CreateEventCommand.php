<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 05.07.16
 * Time: 23:38
 */

namespace CoreBundle\Command;

use CoreBundle\Model\Event\Game\SimpleEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateEventCommand
 * @package CoreBundle\Command
 */
class CreateEventCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:event:create')
            ->setDescription('Create event')
            ->addArgument('frequency')
            ->addArgument('service');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.handler.event")->initEventAndSave(
            (new SimpleEvent())->setFrequency($input->getArgument("frequency")),
            $input->getArgument("service")
        );
    }
}