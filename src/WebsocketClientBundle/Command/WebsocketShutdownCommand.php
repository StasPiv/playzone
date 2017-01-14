<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 27.08.16
 * Time: 13:18
 */

namespace WebsocketClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WebsocketShutdownCommand
 * @package WebsocketClientBundle\Command
 */
class WebsocketShutdownCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('websocket:shutdown_server')
            ->addArgument('ws_server_url', null, '', 'ws://ws.pozitiffchess.net:8081/')
             ->setDescription('Shutdown websocket playzone server');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('ws.playzone.shutdown_server')
             ->shutdownServer($input->getArgument('ws_server_url'));
    }
}