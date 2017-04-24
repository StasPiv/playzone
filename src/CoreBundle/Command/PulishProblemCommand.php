<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 25.04.17
 * Time: 0:21
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PulishProblemCommand
 * @package CoreBundle\Command
 */
class PulishProblemCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:tournament:publish_problem')
            ->setDescription('Publish problem')
            ->addArgument('pgnPath')
            ->addArgument('number')
            ->addArgument('forumId', null, '', 148)
            ->addArgument('threadId', null, '', 33615);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('immortalchessnet.service.event.post_problem')->publishPgnGameByNumber(
            $input->getArgument('pgnPath'),
            $input->getArgument('number'),
            $input->getArgument('forumId'),
            $input->getArgument('threadId')
        );
    }
}