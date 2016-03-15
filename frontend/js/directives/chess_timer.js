/**
 * Created by stas on 13.03.16.
 */
'use strict';

playzoneControllers.directive('chessTimer', function () {
    return {
        restrict: 'C',
        scope: {
            time: '=',
            zeitnot: '='
        },
        templateUrl: 'partials/chess_timer.html'
    }
});