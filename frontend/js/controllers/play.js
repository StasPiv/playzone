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
            if ($scope.game.color === 'b') {
                $scope.my_move = $rootScope.user.id === $scope.game.user_to_move.id;
            } else {
                $scope.my_move = $scope.game.user_white.id === $scope.game.user_to_move.id;
            }

            $scope.my_time = $scope.game.color === 'w' ? $scope.game.time_white : $scope.game.time_black;
            $scope.opponent_time = $scope.game.color === 'b' ? $scope.game.time_white : $scope.game.time_black;

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
                default:
                    WebsocketService.subscribeToGame($scope.game.id);
            }

            $scope.my_time_format = formatTime($scope.my_time, dateFilter);
            $scope.opponent_time_format = formatTime($scope.opponent_time, dateFilter);

            $scope.timer = $interval(function() {
                $scope.game.status != 'play' && $interval.cancel($scope.timer);

                if ($scope.my_move) {
                    $scope.my_time -= 100;
                    $scope.my_time_format = formatTime($scope.my_time, dateFilter);
                    $scope.my_time <= 0 && $interval.cancel($scope.timer) && $scope.timeLost();
                } else {
                    $scope.opponent_time -= 100;
                    $scope.opponent_time_format = formatTime($scope.opponent_time, dateFilter);

                    if ($scope.opponent_time <= 0) {
                        $interval.cancel($scope.timer);
                        $scope.savePgnAndTime();
                    }
                }
            }, 100);
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
        $scope.game.time_white = $scope.game.color === 'w' ? $scope.my_time : $scope.opponent_time;
        $scope.game.time_black = $scope.game.color === 'b' ? $scope.my_time : $scope.opponent_time;
        $scope.game.$savePgn().then(
            function () {
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };
});