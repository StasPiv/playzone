/**
 * Created by stas on 13.03.16.
 */
'use strict';

playzoneControllers.directive('chessTimer', function (dateFilter, $interval) {
    return {
        link: function (scope, element) {
            scope.timer = $interval(
                function () {
                    if (scope.time === 0) {
                        $interval.cancel(scope.timer);
                        scope.fixTime();
                    }

                    if (scope.game.status === 'end') {
                        $interval.cancel(scope.timer);
                    }
                    
                    scope.timeFormat = getBlitzTimeObject(scope.time, dateFilter);

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
        templateUrl: 'partials/chess_timer_with_user.html'
    }
});