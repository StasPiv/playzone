<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 22.06.16
 * Time: 6:42
 */

namespace WebsocketClientBundle\Service;

use CoreBundle\Entity\Game;
use CoreBundle\Entity\GameCall;
use CoreBundle\Entity\Tournament;
use CoreBundle\Entity\User;
use CoreBundle\Exception\Handler\Game\GameNotFoundException;
use CoreBundle\Exception\Handler\User\UserNotFoundException;
use CoreBundle\Model\Game\GameColor;
use CoreBundle\Model\Request\Call\CallDeleteAcceptRequest;
use CoreBundle\Model\Request\Call\CallPostSendRequest;
use CoreBundle\Model\Request\Game\GameGetRequest;
use CoreBundle\Model\Request\Game\GamePutPgnRequest;
use CoreBundle\Model\Request\Tournament\TournamentGetCurrentgameRequest;
use CoreBundle\Model\Request\Tournament\TournamentPostRecordRequest;
use CoreBundle\Model\Request\User\UserPostAuthRequest;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class AjaxService
 * @package WebsocketClientBundle\Service
 */
class AjaxService
{
    use ContainerAwareTrait;

    private $apiHost = 'http://api.playzone.immortalchess.net';

    /**
     * @param $request
     * @param $url
     * @param $method
     * @param $type
     * @return object
     */
    public function processAjax($request, $url, $method, $type)
    {
        return $this->container->get("serializer")->deserialize(
            $this->request(
                    $this->getApiHost() . $url, json_decode(
                    $this->container->get("serializer")->serialize($request, 'json'), true
                ), $method
                ),
            $type,
            'json'
        );
    }

    /**
     * @return string
     */
    public function getApiHost()
    {
        return $this->apiHost;
    }

    /**
     * @param string $apiHost
     * @return AjaxService
     */
    public function setApiHost($apiHost)
    {
        $this->apiHost = $apiHost;

        return $this;
    }

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @return string
     */
    private function request(string $url, array $data, string $method) : string
    {
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            ['Content-Type: application/json','Content-Length: ' . strlen($data_json)]
        );
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        $this->container->get("logger")->error("Url: " . $url);
        $this->container->get("logger")->error("Response: " . $response);

        curl_close($ch);

        return $response;
    }

    /**
     * @param GameGetRequest $request
     * @return Game
     * @throws GameNotFoundException
     */
    public function getGame(GameGetRequest $request) : Game
    {
        try {
            return $this->processAjax(
                    $request,
                    $this->container->get("router")->generate("get_game", [
                        "id" => $request->getId(),
                        "login" => $request->getLogin(),
                        "token" => $request->getToken()
                    ]),
                    'GET',
                    'CoreBundle\Entity\Game'
                );
        } catch (\Exception $e) {
            throw new GameNotFoundException;
        }
    }

    /**
     * @param GamePutPgnRequest $request
     * @return Game
     */
    public function putPgn(GamePutPgnRequest $request) : Game
    {
        $this->container->get("logger")->error("Actual pgn: " . $request->getPgn());
        return $this->processAjax(
                $request,
                $this->container->get("router")->generate("put_game_pgn", [
                    "id" => $request->getId(),
                    "login" => $request->getLogin(),
                    "token" => $request->getToken(),
                    "pgn" => base64_encode($request->getPgn()),
                    "time_white" => $request->getTimeWhite(),
                    "time_black" => $request->getTimeBlack()
                ]),
                'PUT',
                'CoreBundle\Entity\Game'
            );
    }

    /**
     * @param CallPostSendRequest $request
     * @return GameCall
     */
    public function postCall(CallPostSendRequest $request) : GameCall
    {
        return $this->processAjax(
            $request,
            $this->container->get("router")
                ->generate(
                    "post_call_send",
                    [
                        "login" => $request->getLogin(),
                        "token" => $request->getToken(),
                        "color" => $request->getColor(),
                        "time" => [
                            "base" => $request->getTime()->getBase()
                        ]
                    ]
                ),
            "POST",
            'CoreBundle\Entity\GameCall'
        );
    }

    /**
     * @param UserPostAuthRequest $request
     * @return User
     * @throws UserNotFoundException
     */
    public function postAuth(UserPostAuthRequest $request) : User
    {
        return $this->processAjax(
            $request,
            $this->container->get("router")->generate("post_user_auth", [
                "login" => $request->getLogin(),
                "token" => $request->getToken()
            ]),
            "POST",
            'CoreBundle\Entity\User'
        );
    }

    /**
     * @param CallDeleteAcceptRequest $request
     * @return Game
     */
    public function acceptCall(CallDeleteAcceptRequest $request) : Game
    {
        return $this->processAjax(
            $request,
            $this->container->get("router")->generate("delete_call_accept", [
                "login" => $request->getLogin(),
                "token" => $request->getToken(),
                "call_id" => $request->getCallId()
            ]),
            "DELETE",
            'CoreBundle\Entity\Game'
        );
    }

    /**
     * @param TournamentGetCurrentgameRequest $request
     * @return Game
     */
    public function getCurrentTournamentGame(TournamentGetCurrentgameRequest $request) : Game
    {
        return $this->processAjax(
            $request,
            $this->container->get("router")
                ->generate("get_tournament_currentgame", [
                    "login" => $request->getLogin(),
                    "token" => $request->getToken(),
                    "tournament_id" => $request->getTournamentId()
                ]),
            'GET',
            'CoreBundle\Entity\Game'
        );
    }

    /**
     * @param TournamentPostRecordRequest $request
     * @return Tournament
     */
    public function joinTournament(TournamentPostRecordRequest $request) : Tournament
    {
        return $this->processAjax(
            $request,
            $this->container->get("router")
                ->generate("post_tournament_record", [
                    "login" => $request->getLogin(),
                    "token" => $request->getToken(),
                    "tournament_id" => $request->getTournamentId()
                ]),
            'POST',
            'CoreBundle\Entity\Tournament'
        );
    }
}