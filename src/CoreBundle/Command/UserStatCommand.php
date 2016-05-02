<?php

/**
 * Created by PhpStorm.
 * User: stas
 * Date: 01.05.16
 * Time: 21:18
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UserStatCommand
 * @package CoreBundle\Command
 */
class UserStatCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:user:stat')
             ->setDescription('User statistic. Win. lose, draw');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.user.stat")->run();
    }
}