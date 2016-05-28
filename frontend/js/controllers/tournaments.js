/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneControllers.controller('TournamentsCtrl', function ($scope, TournamentRest) {
    $scope.header = 'Tournaments';
    
    $scope.tournaments = TournamentRest.query();
    
    $scope.recordIntoTournament = function (tournament) {
        tournament.$record();
    };
    
    $scope.unrecordFromTournament = function (tournament) {
        tournament.$unrecord();
    };
});