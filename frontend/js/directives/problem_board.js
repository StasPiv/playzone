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
                        scope.beginTime = 1000 * (scope.userProblem.time ? scope.userProblem.time : 300);
                        scope.time = scope.beginTime;
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

                    scope.userProblem.id = scope.userProblem.problem.id;
                    scope.userProblem.solution = scope.game.history()[scope.game.history.length - 1];
                    scope.userProblem.time = scope.beginTime - scope.time;
                    scope.userProblem.$solve().then(
                        function (data) {
                            scope.correct = data.correct;
                            scope.incorrect = !data.correct;
                        }
                    );
                }
                return 'snapback';
            };

            scope.isDisplayHint = false;
            
            scope.showHint = function () {
                scope.userProblem.id = scope.userProblem.problem.id;
                scope.userProblem.solution = 'error';
                scope.userProblem.time = scope.beginTime + 30000;
                scope.userProblem.$solve().then(
                    function (data) {
                        scope.correct = false;
                        scope.incorrect = true;
                        scope.isDisplayHint = true;
                    }
                );
            }
        },
        transclude: false,
        scope: {
            userProblem: '='
        },
        templateUrl: 'partials/problem_board.html?v=0.0.5'
    }
});