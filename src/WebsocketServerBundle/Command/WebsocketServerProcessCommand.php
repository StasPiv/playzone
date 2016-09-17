<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.03.16
 * Time: 19:58
 */

namespace WebsocketServerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WebsocketServerProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->addArgument(
                'host',
                InputArgument::OPTIONAL,
                'Define host',
                'ws.playzone.immortalchess.net'
             )
             ->setName('websocket:server:process')
             ->setDescription('Starting websocket server in process in symfony3');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandLine = 'sudo php ' .
            $this->getContainer()->get('kernel')->getRootDir() .
            '/console websocket:server:start ' . $input->getArgument('host');
        
        $output->writeln($commandLine);
        
        $process = new Process($commandLine);
        $process->start();
    }

}