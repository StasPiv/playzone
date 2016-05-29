/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneControllers.controller('TournamentsCtrl', function ($scope, TournamentRest) {
    $scope.tournaments_new = TournamentRest.query({
        status: "new"
    });

    $scope.tournaments_current = TournamentRest.query({
        status: "current"
    });

    $scope.tournaments_finished = TournamentRest.query({
        status: "end"
    });
    
    $scope.recordIntoTournament = function (tournament) {
        tournament.$record();
    };
    
    $scope.unrecordFromTournament = function (tournament) {
        tournament.$unrecord();
    };
});