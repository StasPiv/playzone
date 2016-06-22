<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.03.16
 * Time: 19:58
 */

namespace WebsocketClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class WebsocketServerProcessCommand
 * @package WebsocketServerBundle\Command
 */
class WebsocketBotProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('websocket:bot:process')
             ->setDescription('Starting websocket bot in process in symfony3');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandLine = 'php ' . $this->getContainer()->get('kernel')->getRootDir() . '/console websocket:bot:start';
        
        $output->writeln($commandLine);
        
        $process = new Process($commandLine);
        $process->start();
    }

}