<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 21.08.16
 * Time: 2:08
 */

namespace ImmortalchessNetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DatabaseConverterCommand
 * @package ImmortalchessNetBundle\Command
 */
class DatabaseConverterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('immortalchessnet:db_converter')
             ->setDescription('Convert db to utf-8');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('immortalchess.service.database_converter')->run();
    }
}