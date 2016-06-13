/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneControllers.controller('TournamentsCtrl', function ($scope, TournamentRest, WebsocketService) {
    $scope.tournaments_new = TournamentRest.query({
        status: "new"
    });

    $scope.tournaments_current = TournamentRest.query({
        status: "current"
    });

    $scope.tournaments_finished = TournamentRest.query({
        status: "end",
        limit: 5
    });
    
    $scope.recordIntoTournament = function (tournament) {
        tournament.checkingLag = true;
        WebsocketService.checkLag(
            function () {
                tournament.checkingLag = false;
                tournament.$record().then(
                    function (data) {

                    },
                    function (errors) {
                        tournament.forbidden = true;
                        tournament.error = errors.data.login
                    }
                )
            }
        );
    };
    
    $scope.unrecordFromTournament = function (tournament) {
        tournament.$unrecord();
    };
});