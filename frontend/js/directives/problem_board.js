/**
 * Created by stas on 09.08.16.
 */
'use strict';

playzoneControllers.directive('problemBoard', function (ProblemRest) {
    return {
        restrict: 'E',
        link: function (scope, element) {
            scope.getRandomProblem = function () {
                scope.userProblem = ProblemRest.get_random(
                    function () {
                        scope.time = 60000;
                        scope.isDisplayHint = scope.correct = scope.incorrect = false;
                        element.loadBoard({
                            position: scope.userProblem.problem.fen,
                            pieceType: 'merida',
                            draggable: true
                        });
                        scope.game = new Chess(scope.userProblem.problem.fen + ' w - - 0 1');
                        element.board.position(scope.game.fen());
                    }
                );
            };
            scope.getRandomProblem();

            element.onMove = function (moveObject) {
                if (!scope.isDisplayHint && scope.game.move(moveObject)) {
                    element.board.move(moveObject.from + '-' + moveObject.to);
                    scope.isDisplayHint = true;

                    var myMove = scope.game.history()[scope.game.history.length - 1];

                    if (scope.userProblem.problem.pgn.indexOf(myMove) !== -1) {
                        scope.userProblem = ProblemRest.solve({
                            id: scope.userProblem.problem.id
                        });
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
            userProblem: '='
        },
        templateUrl: 'partials/problem_board.html?v=0.0.5'
    }
});