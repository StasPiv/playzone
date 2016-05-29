/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneControllers.controller('TournamentCtrl', function ($scope, TournamentRest, $routeParams, $interval) {
    $scope.tournament = TournamentRest.get(
        {
            id: $routeParams.tournamentId
        },
        function () {
            $scope.refresh = function () {
                $scope.tournament.$get();
                return false;
            };

            $scope.refreshInterval = $interval(
                function () {
                    $scope.refresh();

                    if ($scope.tournament.status == 'end') {
                        $interval.cancel($scope.refreshInterval);
                    }
                },
                10000
            );
        }
    );
});