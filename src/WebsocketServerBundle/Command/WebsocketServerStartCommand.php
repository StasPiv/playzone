<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.01.16
 * Time: 22:16
 */

namespace WebsocketServerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\App as RatchetApp;
use WebsocketServerBundle\Service\PlayzoneServer;

class WebsocketServerStartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('websocket:server:start')
            ->addArgument(
                'host',
                InputArgument::OPTIONAL,
                'Define host',
                'localhost'
            )
            ->addArgument(
                'port',
                InputArgument::OPTIONAL,
                'Define port',
                1234
            )
            ->setDescription('Starting websocket server in symfony3');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = new RatchetApp($input->getArgument('host'), $input->getArgument('port'));
        $playzoneServer = new PlayzoneServer();
        $playzoneServer->setContainer($this->getContainer());
        $app->route('/', $playzoneServer, ['*']);
        $output->writeln("Server starting on {$input->getArgument('host')}:{$input->getArgument('port')}...");
        $app->run();
    }
}