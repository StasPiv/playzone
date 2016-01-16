/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, GameService) {
    $scope.games = {};
    GameService.initCallsFromMe($scope);
    GameService.initCallsToMe($scope);
    GameService.initCurrentGames($scope);
});