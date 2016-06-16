/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, EnvService, AudioService, SettingService, ChatRest, $timeout) {
    //$scope.dev = true;
    $scope.chat = ChatRest.query();

    $rootScope.robot = false;
    $scope.boardConfig = {
        pieceType: SettingService.getSetting('Piece type') ?
            SettingService.getSetting('Piece type') : 'leipzig',
        highlightClass: 'highlight1-32417',
        draggable: SettingService.getSetting('Draggable disabled') != 1,
        showNotation: !!SettingService.getSetting('Show notation')
    };

    $scope.gameConfig = {
        zeitnotLimit: SettingService.getSetting('Zeitnot limit') ?
                      SettingService.getSetting('Zeitnot limit') * 1000 :
                      28000
    };

    $scope.game = GameRest.get(
        {
            id: $routeParams.gameId
        }
    );

    $scope.game.$promise.then(
        function () {
            WebsocketService.subscribeToGame($scope.game.id);
            
            if (true || !EnvService.isWebRTC()) { // TODO: need to remove "true" for webRTC support
                return;
            }

            switch ($scope.game.color) {
                case 'w':
                    WebRTCService.createGameRoom($scope.game.id);
                    break;
                case 'b':
                    WebRTCService.joinGameRoom($scope.game.id);
                    WebRTCService.addCallBackLeaveRoom($scope.game.id, function () {
                        $scope.game.$savePgn();
                    });
                    break;
            }
        }
    );

    $scope.resign = function () {
        $scope.game.$resign().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    resign: true
                });
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.draw = function () {
        $scope.opponentOfferDraw =
            ($scope.game.draw && $scope.game.draw !== $scope.game.color) ||
            $scope.game.opponent.login === "Robot";

        if ($scope.opponentOfferDraw) {
            GameRest.acceptDraw(
                {
                    id: $scope.game.id
                },
                function () {
                    WebsocketService.sendGameToObservers($scope.game.id);
                    AudioService.draw();
                    $scope.game.$get();
                }
            );
            return;
        }

        GameRest.offerDraw(
            {
                id: $scope.game.id
            },
            function () {
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.sendWithWebsockets = function () {
        console.log('sendWithWS');
        if ($scope.game.status === 'play') {
            WebsocketService.sendGameToObservers(
                $scope.game.id, 
                window.btoa($scope.game.pgn),
                $scope.game.time_white, 
                $scope.game.time_black, 
                $scope.game.color
            );
        } else {
            WebsocketService.sendGameToObservers($scope.game.id);
        }
    };

    $scope.savePgnAndSendToObservers = function (withoutSaving, move, moveNumber) {
        if (!$scope.game.mine) {
            return;
        }

        if (withoutSaving) {
            $scope.game.move_color = $scope.game.move_color === 'w' ? 'b' : 'w';
            if (move) {
                WebsocketService.sendMoveToObservers(
                    $scope.game.id,
                    move,
                    $scope.game.time_white,
                    $scope.game.time_black,
                    $scope.game.color,
                    moveNumber
                );
            } else {
                $scope.sendWithWebsockets();
            }
            
        }

        $scope.game.$savePgn().then(
            function () {
                $scope.game.opponent.offline = !$rootScope.loginsOnline.searchById($scope.game.opponent.id);
                if (!withoutSaving) {
                    $scope.sendWithWebsockets();
                }
            }
        );
    };

    $scope.publishPgn = function () {
        $scope.game.$publishPgn().then(
            function () {
                $scope.publishLink = "http://immortalchess.net/forum/showthread.php?t=31003&goto=newpost";
            }
        );
    };

    $scope.highlightLastMove = highlightLastMove;

    WebsocketService.addListener('listen_opponent_gone', 'user_gone', function (user) {
        if ($scope.game.opponent && user['login'] === $scope.game.opponent.login) {
            console.log('opponent has gone');
            $scope.savePgnAndSendToObservers();
            $scope.game.opponent.offline = true;
        }
    });

    WebsocketService.addListener('listen_opponent_in', 'user_in', function (user) {
        if ($scope.game && $scope.game.opponent && user['login'] === $scope.game.opponent.login) {
            console.log('opponent has reconnected');
            $scope.game.opponent.offline = false;
        }
    });
});