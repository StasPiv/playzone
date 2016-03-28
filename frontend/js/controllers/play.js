/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, $interval, dateFilter) {
    $scope.boardConfig = {
        pieceType: 'leipzig',
        highlightClass: 'highlight1-32417'
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

            WebsocketService.subscribeToGame($scope.game.id);
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

    $scope.savePgnAndSendToObservers = function () {
        $scope.game.$savePgn().then(
            function () {
                if ($scope.game.status === 'play') {
                    var opponentTime = $scope.game.color === 'w' ? 
                                       $scope.game.time_black :
                                       $scope.game.time_white;
                    WebsocketService.sendGameToObservers($scope.game.id, window.btoa($scope.game.pgn), opponentTime, $scope.game.color);
                } else {
                    WebsocketService.sendGameToObservers($scope.game.id);
                }
            }
        );
    };
});