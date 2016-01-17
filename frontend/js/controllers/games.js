/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, GameService, CallService) {
    $scope.games = {};
    GameService.initCallsFromMe($scope);
    GameService.initCallsToMe($scope);
    GameService.initCurrentGames($scope);

    $scope.removeCall = function(call) {
        CallService.removeCall({
            call: call,
            success: function() {
                $scope.errors = {};
                GameService.initCallsFromMe($scope);
            },
            error: function(data) {
                $scope.errors = data.errors;
            }
        });
    }
});