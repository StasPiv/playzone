/**
 * Created by stas on 09.08.16.
 */
'use strict';

playzoneControllers.directive('problemBoard', function ($rootScope, GameRest, $timeout) {
    return {
        restrict: 'E',
        link: function (scope, element) {
            var board = ChessBoard('board');

            scope.problem.$promise.then(
                function () {
                    board.position(scope.problem.fen);
                }
            );
            
            scope.isDisplayHint = false;
            
            scope.showHint = function () {
                scope.isDisplayHint = true;
            }
        },
        transclude: false,
        scope: {
            problem: '='
        },
        templateUrl: 'partials/problem_board.html?v=0.0.3'
    }
});