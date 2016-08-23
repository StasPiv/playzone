/**
 * Created by stas on 28.05.16.
 */
'use strict';

playzoneControllers.directive('tournamentTable', function () {
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            scope.beautyResult = function (result) {
                return result == 0.5 ? '½' : result;
            };

            scope.tournament.$promise.then(
                function () {
                    scope.contentUrl = 'partials/tournament_table_' + scope.tournament.tournament_params.type + '.html?v=3';
                }
            )
        },
        transclude: true,
        scope: {
            tournament: '='
        },
        template: '<div ng-include="contentUrl"></div>'
    }
});