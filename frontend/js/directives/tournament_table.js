/**
 * Created by stas on 28.05.16.
 */
'use strict';

playzoneControllers.directive('tournamentTable', function () {
    return {
        restrict: 'E',
        link: function(scope, element) {
            scope.beautyResult = function (result) {
                return result == 0.5 ? '½' : result;
            }        
        },
        transclude: true,
        scope: {
            tournament: '='
        },
        templateUrl: 'partials/tournament_table.html'
    }
});