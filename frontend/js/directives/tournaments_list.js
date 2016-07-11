/**
 * Created by stas on 28.05.16.
 */
'use strict';

playzoneControllers.directive('tournamentsList', function () {
    return {
        restrict: 'E',
        link: function(scope, element) {

        },
        transclude: true,
        scope: {
            tournaments: '=',
            record: '=',
            unrecord: '='
        },
        templateUrl: 'partials/tournaments_list.html?v=1.0.2'
    }
});