<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 04.01.16
 * Time: 22:16
 */

namespace WebsocketServerBundle\Command;

use Ratchet\Server\EchoServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\App as RatchetApp;
use WebsocketServerBundle\Service\PlayzoneServer;

/**
 * Class WebsocketServerStartCommand
 * @package WebsocketServerBundle\Command
 */
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
                8081
            )
            ->addArgument(
                'address',
                InputArgument::OPTIONAL,
                'Define port',
                '0.0.0.0'
            )
            ->setDescription('Starting websocket server in symfony3');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = new RatchetApp($input->getArgument('host'), $input->getArgument('port'), $input->getArgument('address'));
        $this->addPlayzoneServer($app);
        $this->addSignalerServer($app, $output);
        $this->addEchoServer($app);
        $output->writeln("Server starting on {$input->getArgument('host')}:{$input->getArgument('port')}...");
        $app->run();
    }

    /**
     * @param RatchetApp $app
     */
    private function addPlayzoneServer(RatchetApp $app)
    {
        $playzoneServer = $this->getContainer()->get("ws.playzone.server");
        $playzoneServer->setContainer($this->getContainer());
        $app->route('/', $playzoneServer, ['*']);
    }

    /**
     * @param RatchetApp $app
     * @param OutputInterface $output
     */
    private function addSignalerServer(RatchetApp $app, OutputInterface $output)
    {
        $signalerServer = $this->getContainer()->get("ws.service.signaling.server_game");
        $signalerServer->setOutput($output);
        $app->route('/signaler', $signalerServer, ['*']);
    }

    /**
     * @param RatchetApp $app
     */
    private function addEchoServer(RatchetApp $app)
    {
        $server = new EchoServer();
        $app->route('/echo3', $server, ['*']);
    }
}