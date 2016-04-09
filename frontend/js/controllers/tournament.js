/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneControllers.controller('TournamentCtrl', function ($scope, TournamentRest, $routeParams) {
    $scope.tournament = TournamentRest.get({id: $routeParams.tournamentId});
});