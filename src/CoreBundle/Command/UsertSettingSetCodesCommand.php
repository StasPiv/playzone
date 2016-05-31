<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 31.05.16
 * Time: 12:19
 */

namespace CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UsertSettingSetCodesCommand
 * @package CoreBundle\Command
 */
class UsertSettingSetCodesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('core:user_setting:codes');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get("core.user_setting.codes")->updateCodes();
    }
}