/**
 * Created by stas on 08.05.16.
 */
'use strict';

playzoneControllers.directive('gamesList', function () {
    return {
        restrict: 'E',
        link: function(scope, element) {
            
        },
        transclude: true,
        scope: {
            games: '='
        },
        templateUrl: 'partials/games_list.html?v=22'
    }
});