/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $routeParams, GameRest) {
    $scope.boardConfig = {
        pieceType: 'leipzig'
    };

    $scope.game = GameRest.get(
        {
            id: $routeParams.gameId
        }
    );
});