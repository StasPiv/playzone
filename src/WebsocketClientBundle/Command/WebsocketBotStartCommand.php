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
             ->addOption("login", "l", InputOption::VALUE_REQUIRED, "Login", "Robot")
             ->addOption("token", "t", InputOption::VALUE_REQUIRED, "Token", "407f20f52463392c43bf6a58b783c4f2")
             ->addOption("skillLevel", "s", InputOption::VALUE_REQUIRED, "Skill level", 20)
             ->addOption("engine", "en", InputOption::VALUE_REQUIRED, "Engine", "stockfish")
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