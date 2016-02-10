/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.directive('chesstempoBoard', function () {
    return {
        restrict: 'C',
        link: function(scope, element) {
            new PgnViewer(scope.boardSettings);
        }
    }
});