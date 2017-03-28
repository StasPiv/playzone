<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 23.03.17
 * Time: 23:41
 */

namespace ImmortalchessNetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SearchCheatersCommand
 * @package ImmortalchessNetBundle\Command
 */
class SearchCheatersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('immortalchessnet:search_cheaters')
             ->addArgument('from', null, '', 0)
             ->addArgument('to', null, '', 1);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('immortalchessnet.service.search_cheater')
            ->searchCheaterAndPublishPost($input->getArgument('from'), $input->getArgument('to'));
    }
}