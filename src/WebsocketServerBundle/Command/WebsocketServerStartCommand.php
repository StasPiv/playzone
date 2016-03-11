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
use WebsocketServerBundle\Service\SignalingServer;

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
        $this->addPlayzoneServer($app);
        $this->addSignalerServer($app, $output);
        $output->writeln("Server starting on {$input->getArgument('host')}:{$input->getArgument('port')}...");
        $app->run();
    }

    /**
     * @param RatchetApp $app
     */
    private function addPlayzoneServer(RatchetApp $app)
    {
        $playzoneServer = new PlayzoneServer();
        $playzoneServer->setContainer($this->getContainer());
        $app->route('/', $playzoneServer, ['*']);
    }

    /**
     * @param RatchetApp $app
     * @param OutputInterface $output
     */
    private function addSignalerServer(RatchetApp $app, OutputInterface $output)
    {
        $signalerServer = new SignalingServer();
        $signalerServer->setOutput($output);
        $app->route('/signaler', $signalerServer, ['*']);
    }
}