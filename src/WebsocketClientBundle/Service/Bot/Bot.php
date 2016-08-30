<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 19.06.16
 * Time: 17:18
 */

namespace WebsocketClientBundle\Service\Bot;

use Chess\Game\ChessGame;
use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Game\GameNotFoundException;
use CoreBundle\Exception\Processor\ProcessorException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Game\GameStatus;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Call\CallSend\Time;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use CoreBundle\Service\Chess\ChessGameService;
use StasPiv\ChessBestMove\Model\EngineConfiguration;
use StasPiv\ChessBestMove\Service\ChessBestMove;
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

    /** @var string */
    private $login = 'Robot';

    /** @var string */
    private $token = '407f20f52463392c43bf6a58b783c4f2';

    /** @var int */
    private $skillLevel = 20;
    
    /** @var string */
    private $engine = 'stockfish';

    /** @var array hashMap */
    private $gameMoves = [];

    /**
     * @param string $apiHost
     * @param string $wsServerUrl
     * @param string $login
     * @param string $token
     * @param int $skillLevel
     * @param string $engine
     */
    public function connect(
        string $apiHost = null,
        string $wsServerUrl = null,
        string $login = null,
        string $token = null,
        int $skillLevel = 20,
        string $engine = 'stockfish'
    )
    {
        if ($apiHost) {
            $this->container->get("ws.playzone.ajax")->setApiHost($apiHost);
        }
        
        if ($wsServerUrl) {
            $this->wsServerUrl = $wsServerUrl;
        }

        if ($login) {
            $this->login = $login;
        }

        if ($token) {
            $this->token = $token;
        }

        if ($skillLevel) {
            $this->skillLevel = $skillLevel;
        }
        
        if ($engine) {
            $this->engine = $engine;
        }

        $this->wsClient = new PlayzoneClient(
            $this->wsServerUrl,
            [
                'timeout' => -1
            ]
        );
        $this->container->get("ws.playzone.client.sender")
                        ->sendIntroduction(
                            $this->wsClient,
                            $login,
                            $token
                        );

        //$this->sendChallenge();

        while (true) {
            $this->resolver();
        }
    }

    private function resolver()
    {
        $rawMessage = $this->wsClient->receive();
        $this->container->get("logger")->error("Raw message: " . json_encode($rawMessage));
        $message = json_decode($rawMessage, true);

        $this->container->get("logger")->addDebug($rawMessage);

        switch (@$message['method']) {
            case 'call_send':
                $data = $message['data'][0];

                if ($data['from_user']['login'] !=
                    $this->login && in_array($this->login, $message['logins'])
                ) {
                    $this->receiveChallenge((int)$data['id']);
                }

                break;
            case 'offer_revenge':
                $this->receiveChallenge((int)$message['data']['id']);
                break;
            case 'call_accept':
                $this->subscribeToGame(@$message['data']['game_id']);
                break;
            case 'new_tournament_round':
                $request = new TournamentGetCurrentgameRequest();
                $request->setLogin($this->login)
                        ->setToken($this->token)
                        ->setTournamentId($message['data']['tournament_id']);

                try {
                    $game = $this->getCurrentTournamentGame($request);
                    $this->subscribeToGame($game->getId());
                } catch (\Exception $e) {
                    break;
                }

                $this->subscribeToGame($game->getId());
                break;
            case 'new_tournament':
                $request = new TournamentPostRecordRequest();
                $request->setLogin($this->login)
                        ->setToken($this->token)
                        ->setTournamentId($message['data']['tournament_id']);

                try {
                    $this->joinTournament($request);
                } catch (\Exception $e) {
                    break;
                }
                break;
            case 'user_in':
                $this->sendChallenge([$message['data']['login']]);
                break;
        }

        $this->container->get("logger")->error("Game ids: " . json_encode($this->gameIds));
        foreach ($this->gameIds as $id) {
            if (@$message['method'] == 'game_pgn_' . $id) {
                $this->sendBestMove(@$message['data']);
            }
        }
    }

    /**
     * @param array $logins
     * @return $this
     */
    private function sendChallenge(array $logins = [])
    {
        $robot = $this->getRobotUser();

        $request = new CallPostSendRequest();
        $request->setLogin($robot->getLogin())
                ->setToken($robot->getToken())
                ->setColor(GameColor::RANDOM)
                ->setTime(
                    (new Time())->setBase(180000)
                );

        try {
            $gameCall = $this->postCall($request);
        } catch (ProcessorException $e) {
            $this->container->get("logger")->error($e->getMessage());
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
                    )
                    ->setLogins($logins),
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
            $game = $this->acceptCall($request);
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
        $this->container->get("logger")->error("Received data " . json_encode($data));
        $this->container->get("logger")->error("Moves map " . json_encode($this->moveNumbersMap));

        if (isset($data['move'])) {
            $this->container->get("logger")->error("Receive move: " . json_encode($data['move']));
            $this->gameMoves[$data['game_id']][] = $data['move'];
        }

        if (
            !isset($data['fen']) || 
            isset($this->moveNumbersMap[$data['game_id']]) && 
            $this->moveNumbersMap[$data['game_id']] != $data['moveNumber'] - 1
        ) {
            return $this;
        }

        $this->container->get("logger")->error("Go ahead");
        $robotUser = $this->getRobotUser();

        $request = new GameGetRequest();

        $request->setId($data['game_id'])
            ->setLogin($robotUser->getLogin())
            ->setToken($robotUser->getToken());

        $game = $this->getGame($request);

        $fen = $data['fen'];
        $this->container->get("logger")->error("Receive " . $fen);

        $engineConfiguration = new EngineConfiguration($this->engine);

        $engineConfiguration->setWtime((int)$data['time_white'])
                            ->setBtime((int)$data['time_black']);

        $engineConfiguration->addOption('Skill Level', $this->skillLevel);

        $bestMove = (new ChessBestMove($engineConfiguration, $this->container->get('logger')))->getBestMoveFromFen($fen);

        $this->container->get("logger")->error(
            "Best move: " . $bestMove->getFrom().$bestMove->getTo()
        );

        $request = new GamePutPgnRequest();

        $request->setId($data['game_id'])
                ->setToken($robotUser->getToken())
                ->setLogin($robotUser->getLogin());
        
        $endTime = microtime(true);
        $delay = 1000 * ($endTime - $startTime);

        if ($game->getUserWhite()->getLogin() == $robotUser->getLogin()) {
            $request->setTimeWhite((int)($data['time_white'] - $delay))->setTimeBlack($data['time_black']);
        } else {
            $request->setTimeBlack((int)($data['time_black'] - $delay))->setTimeWhite($data['time_white']);
        }

        $move = [
            'from' => $bestMove->getFrom(),
            'to' => $bestMove->getTo(),
            'promotion' => $bestMove->getPromotion()
        ];

        $this->gameMoves[$data['game_id']][] = $move;

        $chessGame = $this->container->get("core.service.chess.game");
        $chessGame->resetGame($fen);
        $chessGame->moveSquare($move['from'], $move['to'], $move['promotion']);

        $this->container->get("logger")->error("Json move: " . json_encode($move));

        $chessGameForSave = new ChessGameService();
        $chessGameForSave->resetGame();
        foreach ($this->gameMoves[$data['game_id']] as $move) {
            $chessGameForSave->moveSquare($move['from'], $move['to'], @$move['promotion']);
        }

        $pgn = $chessGameForSave->getPgn();

        $this->container->get("logger")->error("Pgn: " . $pgn);

        $request->setPgn($pgn);

        $this->wsClient->send(
            $this->container->get("serializer")->serialize(
                (new PlayzoneMessage())->setScope("send_to_game_observers")
                    ->setMethod("send_pgn_to_observers")
                    ->setData(
                        [
                            'game_id' => $data['game_id'],
                            'move' => $move,
                            'moveNumber' => $this->moveNumbersMap[$data['game_id']] = $data['moveNumber'] + 1,
                            'time_white' => (int)$request->getTimeWhite(),
                            'time_black' => (int)$request->getTimeBlack(),
                            'color' => GameColor::getOppositeColor($data['color']),
                            'fen' => $chessGame->renderFen()
                        ]
                    ),
                "json"
            )
        );

        try {
            $game = $this->putPgn($request);

            if ($game->getStatus() == GameStatus::END) {
                $this->container->get("logger")->error("Send about game finish");
                $this->wsClient->send(
                    $this->container->get("serializer")->serialize(
                        (new PlayzoneMessage())->setScope("send_to_game_observers")
                            ->setMethod("send_pgn_to_observers")
                            ->setData(
                                [
                                    'game_id' => $game->getId()
                                ]
                            ),
                        "json"
                    )
                );
            }
        } catch (\Exception $e) {
            $this->container->get("logger")->error("Exception: " . $e->getMessage());
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

        if ($game->getUserWhite() == $this->login) {
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
     * @return User
     */
    private function getRobotUser() : User
    {
        $request = new UserPostAuthRequest();
        $request->setLogin($this->login)->setToken($this->token)->setPassword($this->token);
        
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
    private function postAuth(UserPostAuthRequest $request) : User
    {
        return $this->container->get("ws.playzone.ajax")->postAuth($request);
    }

    /**
     * @param CallDeleteAcceptRequest $request
     * @return Game
     */
    private function acceptCall(CallDeleteAcceptRequest $request) : Game
    {
        return $this->container->get("ws.playzone.ajax")->acceptCall($request);
    }

    /**
     * @param TournamentGetCurrentgameRequest $request
     * @return Game
     */
    private function getCurrentTournamentGame(TournamentGetCurrentgameRequest $request) : Game
    {
        return $this->container->get("ws.playzone.ajax")->getCurrentTournamentGame($request);
    }

    /**
     * @param TournamentPostRecordRequest $request
     * @return Tournament
     */
    private function joinTournament(TournamentPostRecordRequest $request) : Tournament
    {
        return $this->container->get("ws.playzone.ajax")->joinTournament($request);
    }

}