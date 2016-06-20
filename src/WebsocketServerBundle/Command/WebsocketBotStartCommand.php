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

/**
 * Class WebsocketBotProcessCommand
 * @package WebsocketServerBundle\Command
 */
class WebsocketBotStartCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('websocket:bot:start')
             ->addArgument("host")
             ->setDescription('Starting websocket bot');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("ws.playzone.bot")->connect($input->getArgument("host"));
    }

}