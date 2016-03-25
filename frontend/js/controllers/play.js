/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, $interval, dateFilter) {
    $scope.boardConfig = {
        pieceType: 'leipzig'
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

    $scope.timeLost = function () {
        $scope.game.$timeLost().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    resign: true
                });
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.savePgnAndTime = function () {
        $scope.game.$savePgn().then(
            function () {
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };
});