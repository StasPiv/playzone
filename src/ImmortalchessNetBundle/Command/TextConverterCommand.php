<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 20.08.16
 * Time: 0:08
 */

namespace ImmortalchessNetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TextConverterCommand
 * @package ImmortachessNetBundle\Command
 */
class TextConverterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('immortalchessnet:text_converter')
            ->setDescription('Convert text to utf-8')
            ->addArgument('fileInName')
            ->addArgument('fileOutName');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("immortalchessnet.service.text.converter")->convertTextFile(
            $input->getArgument('fileInName'),
            $input->getArgument('fileOutName')
        );
    }
}