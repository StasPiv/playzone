/**
 * Created by stas on 09.08.16.
 */
'use strict';

playzoneControllers.directive('problemBoard', function ($rootScope, GameRest, $timeout) {
    return {
        restrict: 'E',
        link: function (scope, element) {
            scope.correct = scope.incorrect = false;
            scope.problem.$promise.then(
                function () {
                    element.loadBoard({
                        position: scope.problem.fen,
                        pieceType: 'merida',
                        draggable: true
                    });
                    scope.game = new Chess(scope.problem.fen + ' w - - 0 1');
                    element.board.position(scope.game.fen());
                }
            );

            element.onMove = function (moveObject) {
                if (scope.game.move(moveObject)) {
                    element.board.move(moveObject.from + '-' + moveObject.to);
                    scope.isDisplayHint = true;

                    var myMove = scope.game.history()[scope.game.history.length - 1];

                    if (scope.problem.pgn.indexOf(myMove) !== -1) {
                        scope.correct = true;
                    } else {
                        scope.incorrect = true;
                    }
                }
                return 'snapback';
            };

            scope.isDisplayHint = false;
            
            scope.showHint = function () {
                scope.isDisplayHint = true;
            }
        },
        transclude: false,
        scope: {
            problem: '='
        },
        templateUrl: 'partials/problem_board.html?v=0.0.4'
    }
});