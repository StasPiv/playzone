/**
 * Created by stas on 13.03.16.
 */
'use strict';

playzoneControllers.directive('chessTimer', function (dateFilter, $interval, $timeout) {
    return {
        link: function (scope, element) {

            var dateBegin = new Date();

            var testCount = 100000;
            for (var i=0; i< testCount; i++) {
                getBlitzTimeObject(i, dateFilter);
            }

            var dateEnd = new Date();

            console.log("delay: ", testCount / ((dateEnd.getTime() - dateBegin.getTime()) / 1000) );

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