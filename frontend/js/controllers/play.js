/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, EnvService, AudioService) {
    $scope.boardConfig = {
        pieceType: 'leipzig',
        highlightClass: 'highlight1-32417',
        draggable: !EnvService.isMobile()
    };

    $scope.gameConfig = {
        zeitnotLimit: 28000
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
        $scope.opponentOfferDraw = $scope.game.draw && $scope.game.draw !== $scope.game.color;

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
        )
    };

    WebsocketService.addListener('listen_opponent_gone', 'user_gone', function (user) {
        if (user['login'] === $scope.game.opponent.login) {
            console.log('opponent has gone');
            $scope.savePgnAndSendToObservers();
            $scope.game.opponent.offline = true;
        }
    });

    WebsocketService.addListener('listen_opponent_in', 'user_in', function (user) {
        if ($scope.game && user['login'] === $scope.game.opponent.login) {
            console.log('opponent has reconnected');
            $scope.game.opponent.offline = false;
        }
    });

});