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

    WebRTCService.joinOrCreateGameRoom($routeParams.gameId);
    WebRTCService.addCallBackLeaveRoom($routeParams.gameId, function () {
        $scope.game.$savePgn();
    });
});