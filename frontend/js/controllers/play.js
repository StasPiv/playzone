/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, EnvService, AudioService, SettingService) {
    $rootScope.robot = false;
    $scope.boardConfig = {
        pieceType: SettingService.getSetting('Piece type') ?
            SettingService.getSetting('Piece type') : 'leipzig',
        highlightClass: 'highlight1-32417',
        draggable: SettingService.getSetting('Draggable disabled') != 1
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
            $scope.game.$acceptDraw().then(
                function () {
                    WebsocketService.sendGameToObservers($scope.game.id);
                    AudioService.draw();
                }
            );
            return;
        }

        $scope.game.$offerDraw().then(
            function () {
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.sendWithWebsockets = function () {
        console.log('sendWithWS');
        if ($scope.game.status === 'play') {
            console.log($scope.game.pgn);
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

    $scope.savePgnAndSendToObservers = function (withoutSaving) {
        if (withoutSaving) {
            $scope.sendWithWebsockets();
        }

        $scope.game.$savePgn().then(
            function () {
                $scope.game.opponent.offline =
                    ($rootScope.loginsOnline.indexOf($scope.game.opponent.login) === -1);
                if (!withoutSaving) {
                    $scope.sendWithWebsockets();
                }
            }
        );
    };

    WebsocketService.addListener('listen_opponent_gone', 'user_gone', function (user) {
        if (user['login'] === $scope.game.opponent.login) {
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