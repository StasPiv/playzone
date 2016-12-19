<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.12.16
 * Time: 18:39
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CalculateGranPriCommand
 * @package CoreBundle\Command
 */
class CalculateGranPriCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:granpri:calculate')
            ->setDescription('Calculate granpri table')
            ->addArgument('publish');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('core.service.granpri.calculator')->process(
            (bool)$input->getArgument('publish')
        );
    }
}