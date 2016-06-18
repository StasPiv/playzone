/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, $rootScope, $location, CallRest, GameRest, WebsocketService) {
    $scope.current = GameRest.query({status: "play", user:"all"});

    WebsocketService.addListener("listen_finished_games", "game_finish", function(data) {
        var game = $scope.current.searchById(data.game_id);
        if (game) {
            game.$get();
        }
    });
});