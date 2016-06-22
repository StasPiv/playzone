<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 29.05.16
 * Time: 21:22
 */

namespace WebsocketClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageMethod;
use WebsocketServerBundle\Model\Message\Client\PlayzoneClientMessageScope;
use WebsocketServerBundle\Model\Message\Client\Tournament\TournamentMesssageNewRound;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketClientBundle\Service\Client\PlayzoneClientSender;

/**
 * Class WebsocketClientTestCommand
 * @package WebsocketServerBundle\Command
 */
class WebsocketClientTestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('websocket:client:test');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get("ws.playzone.client");

        $this->getPlayzoneClientSender()->sendIntroductionFromRobot($client);

        $this->getPlayzoneClientSender()->send(
            $client,
            (new PlayzoneMessage())
                ->setMethod(PlayzoneClientMessageMethod::NEW_TOURNAMENT_ROUND)
                ->setScope(PlayzoneClientMessageScope::SEND_TO_USERS)
                ->setData(
                    $this->getContainer()->get("core.service.playzone_serializer")->toArray(
                        new TournamentMesssageNewRound(1)
                    )
                )
        );
    }

    /**
     * @return PlayzoneClientSender
     */
    private function getPlayzoneClientSender()
    {
        return $this->getContainer()->get("ws.playzone.client.sender");
    }
}