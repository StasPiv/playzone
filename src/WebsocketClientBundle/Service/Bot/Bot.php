<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.06.16
 * Time: 17:18
 */

namespace WebsocketClientBundle\Service\Bot;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Game\GameNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallSend\Time;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use WebsocketServerBundle\Model\Message\PlayzoneMessage;
use WebsocketClientBundle\Service\PlayzoneClient;

/**
 * Class Bot
 * @package WebsocketServerBundle\Service\Bot
 *
 * TODO: refactor entire class
 */
class Bot
{
    use ContainerAwareTrait;
    
    /** @var string */
    private $wsServerUrl = "ws://ws.playzone.immortalchess.net:8081/";

    /** @var PlayzoneClient */
    private $wsClient;

    /** @var array */
    private $gameIds = [];
    
    /** @var array */
    private $moveNumbersMap = [];

    /**
     * @param string $apiHost
     * @param string $wsServerUrl
     */
    public function connect(string $apiHost = null, string $wsServerUrl = null)
    {
        if ($apiHost) {
            $this->container->get("ws.playzone.ajax")->setApiHost($apiHost);
        }
        
        if ($wsServerUrl) {
            $this->wsServerUrl = $wsServerUrl;
        }        

        $this->wsClient = new PlayzoneClient(
            $this->wsServerUrl,
            [
                'timeout' => -1
            ]
        );
        $this->container->get("ws.playzone.client.sender")->sendIntroductionFromRobot($this->wsClient);

        while (true) {
            $this->resolver();
        }
    }

    private function resolver()
    {
        $rawMessage = $this->wsClient->receive();
        $message = json_decode($rawMessage, true);

        $this->container->get("logger")->addDebug($rawMessage);

        switch (@$message['method']) {
            case 'call_send':
                $data = $message['data'][0];

                if ($data['from_user']['login'] != 'Robot') {
                    sleep(15);
                    $this->receiveChallenge((int)$data['id']);
                }

                break;
            case 'call_accept':
                $this->subscribeToGame(@$message['data']['game_id']);
                break;
            case 'new_tournament_round':
                $robotUser = $this->getRobotUser();
                $request = new TournamentGetCurrentgameRequest();
                $request->setLogin($robotUser->getLogin())
                        ->setToken($robotUser->getToken())
                        ->setTournamentId($message['data']['tournament_id']);

                try {
                    $game = $this->container->get("core.handler.tournament")
                        ->processGetCurrentgame($request);
                } catch (ProcessorException $e) {
                    break;
                }

                $this->subscribeToGame($game->getId());
                break;
        }

        foreach ($this->gameIds as $id) {
            if (@$message['method'] == 'game_pgn_' . $id) {
                $this->sendBestMove(@$message['data']);
            }
        }
    }

    /**
     * @return $this
     */
    private function sendChallenge()
    {
        $robot = $this->getRobotUser();

        $request = new CallPostSendRequest();
        $request->setLogin($robot->getLogin())
                ->setToken($robot->getToken())
                ->setColor(GameColor::RANDOM)
                ->setTime(
                    (new Time())->setBase(300000)
                );

        try {
            $gameCall = $this->postCall($request);
        } catch (ProcessorException $e) {
            $this->container->get("logger")->warning($e->getMessage());
            return $this;
        }

        $this->container->get("logger")->debug(
            $this->container->get("serializer")->serialize($gameCall, "json")
        );

        $this->wsClient->send(
            $this->container->get("serializer")->serialize(
                (new PlayzoneMessage())->setScope("send_to_users")
                    ->setMethod("call_send")
                    ->setData(
                        json_decode(
                            $this->container->get("serializer")->serialize($gameCall, "json"),
                            true
                        )
                    ),
                "json"
            )
        );

        return $this;
    }

    /**
     * @param int $callId
     * @return $this
     */
    private function receiveChallenge(int $callId)
    {
        $this->container->get("logger")->debug("Call Id $callId");

        $robot = $this->getRobotUser();

        $request = new CallDeleteAcceptRequest();
        $request->setLogin($robot->getLogin())
                ->setToken($robot->getToken())
                ->setCallId($callId);

        try {
            $game = $this->container->get("core.handler.game.call")->processDeleteAccept($request);
        } catch (ProcessorException $e) {
            $this->container->get("logger")->warning($e->getFile() . " " . $e->getLine());
            return $this;
        }

        $this->container->get("logger")->debug(
            $this->container->get("serializer")->serialize($game, "json")
        );

        $this->wsClient->send(
            $this->container->get("serializer")->serialize(
                (new PlayzoneMessage())->setScope("send_to_users")
                    ->setMethod("call_accept")
                    ->setData(
                        [
                            'game_id' => $game->getId(),
                            'call_id' => $callId
                        ]
                    ),
                "json"
            )
        );

        $this->subscribeToGame($game->getId());

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \WebSocket\BadOpcodeException
     */
    private function sendBestMove(array $data)
    {
        $startTime = microtime(true);
        $this->container->get("logger")->debug("Received data " . json_encode($data));
        $this->container->get("logger")->debug("Moves map " . json_encode($this->moveNumbersMap));

        if (
            !isset($data['fen']) || 
            isset($this->moveNumbersMap[$data['game_id']]) && 
            $this->moveNumbersMap[$data['game_id']] != $data['moveNumber'] - 1
        ) {
            return $this;
        }

        $fen = $data['fen'];
        $this->container->get("logger")->debug("Receive " . $fen);

        $bestMove = $this->container->get("core.service.chess")
                         ->getBestMoveFromFen($fen, (int)$data['time_white'], (int)$data['time_black']);

        $this->container->get("logger")->debug($bestMove);

        $robotUser = $this->getRobotUser();

        $request = new GameGetRequest();

        $request->setId($data['game_id'])
            ->setLogin($robotUser->getLogin())
            ->setToken($robotUser->getToken());

        $game = $this->getGame($request);

        $request = new GamePutPgnRequest();

        $request->setId($data['game_id'])
                ->setToken($robotUser->getToken())
                ->setLogin($robotUser->getLogin());
        
        $endTime = microtime(true);
        $delay = 1000 * ($endTime - $startTime);

        if ($game->getUserWhite() == $robotUser) {
            $request->setTimeWhite($data['time_white'] - $delay)->setTimeBlack($data['time_black']);
        } else {
            $request->setTimeBlack($data['time_black'] - $delay)->setTimeWhite($data['time_white']);
        }

        $this->wsClient->send(
            $this->container->get("serializer")->serialize(
                (new PlayzoneMessage())->setScope("send_to_game_observers")
                    ->setMethod("send_pgn_to_observers")
                    ->setData(
                        [
                            'game_id' => $data['game_id'],
                            'move' => [
                                'from' => substr($bestMove, 0, 2),
                                'to' => substr($bestMove, 2, 2),
                                'promotion' => 'q'
                            ],
                            'moveNumber' => $this->moveNumbersMap[$data['game_id']] = $data['moveNumber'] + 1,
                            'time_white' => (int)$request->getTimeWhite(),
                            'time_black' => (int)$request->getTimeBlack(),
                            'color' => GameColor::getOppositeColor($data['color'])
                        ]
                    ),
                "json"
            )
        );

        try {
            $this->putPgn($request);
        } catch (\Exception $e) {
            $this->container->get("logger")->error($e->getMessage());
        }

        return $this;
    }

    /**
     * @param int $gameId
     * @return $this
     * @throws \WebSocket\BadOpcodeException
     */
    private function subscribeToGame(int $gameId)
    {
        $this->container->get("logger")->debug(
            $this->container->get("serializer")->serialize("Subscribe $gameId", "json")
        );

        $this->wsClient->send(
            $this->container->get("serializer")->serialize(
                (new PlayzoneMessage())->setScope("subscribe_to_game")
                    ->setMethod("subscribe_to_game")
                    ->setData(
                        [
                            'game_id' => $gameId
                        ]
                    ),
                "json"
            )
        );

        $this->gameIds[$gameId] = $gameId;

        try {
            $user = $this->getRobotUser();

            $request = new GameGetRequest();

            $request->setId($gameId)
                    ->setLogin($user->getLogin())
                    ->setToken($user->getToken());

            $game = $this->getGame($request);
        } catch (GameNotFoundException $e) {
            $this->container->get("logger")->error("Game #$gameId is not found");
            return $this;
        }

        if ($game->getUserWhite() == 'Robot') {
            $this->sendBestMove(
                [
                    'game_id' => $game->getId(),
                    'moveNumber' => 0,
                    'fen' => '',
                    'time_white' => $game->getTimeWhite(),
                    'time_black' => $game->getTimeBlack(),
                    'color' => GameColor::BLACK
                ]
            );
        }

        return $this;
    }

    /**
     * @return \CoreBundle\Entity\User
     */
    private function getRobotUser()
    {
        $request = new UserPostAuthRequest();
        $request->setLogin("Robot")->setToken("407f20f52463392c43bf6a58b783c4f2")->setPassword("no matter");
        
        return $this->postAuth($request);
    }

    /**
     * @param GameGetRequest $request
     * @return Game
     * @throws GameNotFoundException
     */
    private function getGame(GameGetRequest $request) : Game
    {
        return $this->container->get("ws.playzone.ajax")->getGame($request);
    }

    /**
     * @param GamePutPgnRequest $request
     * @return Game
     */
    private function putPgn(GamePutPgnRequest $request) : Game
    {
        return $this->container->get("ws.playzone.ajax")->putPgn($request);
    }

    /**
     * @param CallPostSendRequest $request
     * @return GameCall
     */
    private function postCall(CallPostSendRequest $request) : GameCall
    {
        return $this->container->get("ws.playzone.ajax")->postCall($request);
    }

    /**
     * @param UserPostAuthRequest $request
     * @return User
     */
    private function postAuth(UserPostAuthRequest $request)
    {
        return $this->container->get("ws.playzone.ajax")->postAuth($request);
    }

}