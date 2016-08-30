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
use Symfony\Component\Console\Input\InputOption;
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
             ->addArgument("apiHost")
             ->addArgument("wsServerUrl")
             ->addOption("login", "l", InputOption::VALUE_REQUIRED, "Login", "Glaurung")
             ->addOption("token", "t", InputOption::VALUE_REQUIRED, "Token", "994b884e706a9bb26a19906364a3b2b3")
             ->addOption("skillLevel", "s", InputOption::VALUE_REQUIRED, "Skill level", 20)
             ->addOption("engine", "en", InputOption::VALUE_REQUIRED, "Engine", "glaurung")
             ->setDescription('Starting websocket bot');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("ws.playzone.bot")
             ->connect(
                 $input->getArgument("apiHost"), 
                 $input->getArgument("wsServerUrl"), 
                 $input->getOption("login"),
                 $input->getOption("token"), 
                 $input->getOption("skillLevel"),
                 $input->getOption("engine")
             );
    }

}