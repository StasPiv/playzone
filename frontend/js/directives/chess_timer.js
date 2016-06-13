/**
 * Created by stas on 13.03.16.
 */
'use strict';

playzoneControllers.directive('chessTimer', function (dateFilter, $interval, $timeout, LogRest) {
    return {
        link: function (scope, element) {
            var counter = 0;
            var dateBegin = new Date();
            var timeBegin = dateBegin.getTime();
            scope.timer = $interval(
                function () {
                    if (scope.time === 0) {
                        $interval.cancel(scope.timer);
                        scope.fixTime();
                    }

                    if (scope.game.status === 'end') {
                        $interval.cancel(scope.timer);
                    }

                    $timeout(
                        function () {
                            scope.timeFormat = getBlitzTimeObject(scope.time, dateFilter);
                        },
                        0
                    );

                    if (counter++ % 100 === 0) {
                        var dateCurrent = new Date();
                        var timeCurrent = dateCurrent.getTime();

                        var oneIncrementTime = (timeCurrent - timeBegin) / 100;
                        LogRest.log(
                            "",
                            {
                                message: "[Timer rate] " + (oneIncrementTime / 100)
                            }
                        );
                        dateBegin = new Date();
                        timeBegin = dateBegin.getTime();
                    }

                    if (!scope.current) {
                        return;
                    }

                    scope.time -= 100;
                },
                100
            );
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