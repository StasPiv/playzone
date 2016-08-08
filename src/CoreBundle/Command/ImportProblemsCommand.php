<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 09.08.16
 * Time: 1:18
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportProblemsCommand
 * @package CoreBundle\Command
 */
class ImportProblemsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:problems:import')
            ->setDescription('Import problems')
            ->addArgument('file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->getContainer()->get("core.service.import_problems")
                      ->import($input->getArgument("file"));

        $output->writeln("Imported $count problems");
    }
}