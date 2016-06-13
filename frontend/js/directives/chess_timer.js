/**
 * Created by stas on 13.03.16.
 */
'use strict';

playzoneControllers.directive('chessTimer', function (dateFilter, $interval) {
    return {
        link: function (scope) {
            scope.$watch(
                'current',
                function (newVal, oldVal) {
                    newVal ? (scope.timer = startTimer()) : stopTimer();
                }
            );

            scope.$watch(
                'time',
                function (newVal, oldVal) {
                    if (newVal <= 0) {
                        scope.fixTime();
                        stopTimer();
                    }
                }
            );

            var startTimer = function () {
                return $interval(
                    function () {
                        scope.timeFormat = getBlitzTimeObject(scope.time-=100, dateFilter);
                    },
                    100
                );
            };

            var stopTimer = function () {
                $interval.cancel(scope.timer);
            };

            scope.timeFormat = getBlitzTimeObject(scope.time, dateFilter);
        },
        restrict: 'E',
        transclude: true,
        scope: {
            time: '=',
            zeitnot: '=',
            current: '=',
            game: '=',
            user: '=',
            fixTime: '&fixTime'
        },
        templateUrl: 'partials/chess_timer_new.html'
    }
});