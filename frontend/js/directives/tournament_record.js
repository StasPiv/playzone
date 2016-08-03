/**
 * Created by stas on 16.06.16.
 */
'use strict';

playzoneControllers.directive('tournamentRecord', function (WebsocketService) {
    return {
        restrict: 'E',
        link: function(scope) {
            scope.record = function () {
                scope.tournament.checkingLag = true;
                WebsocketService.checkLag(
                    function () {
                        scope.tournament.checkingLag = false;
                        scope.tournament.$record().then(
                            function (data) {
                                scope.tournament.$get();
                            },
                            function (errors) {
                                scope.tournament.forbidden = true;
                                scope.tournament.error = errors.data.login
                            }
                        )
                    }
                );
            };

            scope.unrecord = function () {
                scope.tournament.$unrecord().then(
                    function () {
                        scope.tournament.$get();
                    }
                );
            };
        },
        transclude: true,
        scope: {
            tournament: '='
        },
        templateUrl: 'partials/tournament_record.html?v=0.1'
    }
});