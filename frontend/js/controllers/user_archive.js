/**
 * Created by stas on 08.05.16.
 */
'use strict';

playzoneControllers.controller('UserArchiveCtrl', function ($scope, $routeParams, GameRest) {
    $scope.games = GameRest.query(
        {
            status: "end",
            user:$routeParams.userId,
            limit: 20
        }
    );
});