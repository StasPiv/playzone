/**
 * Created by stas on 11.07.16.
 */
'use strict';

playzoneControllers.directive('gameParams', function () {
    return {
        restrict: 'E',
        link: function(scope, element) {

        },
        transclude: true,
        scope: {
            params: '='
        },
        templateUrl: 'partials/game_params.html'
    }
});