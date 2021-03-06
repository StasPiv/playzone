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
                    scope.timeFormat = getBlitzTimeObject(scope.time);
                    newVal ? startTimer() : stopTimer();
                }
            );

            var startTimer = function () {
                scope.timer = $interval(
                    function () {
                        scope.time-=scope.refreshTime;
                        scope.timeFormat = getBlitzTimeObject(scope.time);

                        if (scope.time <= 0) {
                            scope.fixTime();
                            stopTimer();
                        }
                    },
                    scope.refreshTime
                );
            };

            var stopTimer = function () {
                $interval.cancel(scope.timer);
            };

            scope.timeFormat = getBlitzTimeObject(scope.time);
        },
        restrict: 'E',
        transclude: true,
        scope: {
            time: '=',
            zeitnot: '=',
            current: '=',
            game: '=',
            user: '=',
            refreshTime: '=',
            fixTime: '&fixTime'
        },
        templateUrl: 'partials/chess_timer_new.html'
    }
});