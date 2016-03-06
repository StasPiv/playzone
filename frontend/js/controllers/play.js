/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService) {
    $scope.boardConfig = {
        pieceType: 'leipzig'
    };

    $scope.game = GameRest.get(
        {
            id: $routeParams.gameId
        }
    );

    $scope.game.$promise.then(
        function () {
            if ($scope.game.color === 'w') {
                WebRTCService.createGameRoom($scope.game.id);
            } else {
                WebRTCService.joinGameRoom($scope.game.id);
                WebRTCService.addCallBackLeaveRoom($routeParams.gameId, function () {
                    $scope.game.$savePgn();
                });
            }
        }
    );
});