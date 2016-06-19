/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.directive('pgnNotation', function ($rootScope, GameRest, $timeout) {
    return {
        restrict: 'E',
        link: function (scope, element) {
            scope.publishFen = function () {
                GameRest.publishFen(
                    "",
                    {
                        id: scope.game.id,
                        fen: $rootScope.chessBoard.fen()
                    },
                    function () {
                        scope.publishLink = "http://immortalchess.net/forum/showthread.php?t=31003&goto=newpost";

                        $timeout(
                            function () {
                                scope.publishLink = null;
                            },
                            7000
                        )
                    }
                )
            };

            scope.currentIndex = $rootScope.chess.history().length;

            function goToIndex(currentIndex) {

                var chess = new Chess();
                $.each(
                    $rootScope.chess.history({verbose: true}),

                    function (index, move) {
                        if (index > currentIndex) {
                            return;
                        }
                        chess.move(move);
                    }
                );

                $rootScope.chessBoard.position(chess.fen());


                highlightMoveInNotation(currentIndex);

                if (currentIndex < 0) {
                    $('#board').find('[class*="square"]').removeClass('highlight1-32417');
                    return;
                }

                highlightLastMove(scope, '#board', chess.history({verbose: true})[chess.history().length - 1], $rootScope.chess);
            }

            function highlightMoveInNotation(index) {
                $(element).find('.move').removeClass('current');
                $(element).find('.move-' + index).addClass('current');
            }

            $(element).on('click', '.move', function () {
                goToIndex(scope.currentIndex = $(this).data('move-index'));
            });

            $(element).on('click', '.start', function() {
                goToIndex(scope.currentIndex = -1);
            });

            $(element).on('click', '.end', function() {
                goToIndex(scope.currentIndex = $rootScope.chess.history().length);
            });

            $(element).on('click', '.go', function() {
                if (scope.currentIndex >= $rootScope.chess.history().length) {
                    return;
                }
                goToIndex(++scope.currentIndex);
            });

            $(element).on('click', '.back', function() {
                if (scope.currentIndex < -1) {
                    scope.currentIndex = -1;
                }

                goToIndex(--scope.currentIndex);
            });

            $(window).on('keydown', function(event) {
                var key = event.charCode ? event.charCode : event.keyCode ? event.keyCode : 0;

                var left = 37;
                var right = 39;

                switch (key) {
                    case left:
                        $(element).find('.back').click();
                        break;
                    case right:
                        $(element).find('.go').click();
                        break;
                }
            });

            $(element).on('click', '.flip', function() {
                $rootScope.chessBoard.flip();
            });
        },
        transclude: false,
        scope: {
            game: '='
        },
        templateUrl: 'partials/pgn_notation.html?rand=' + Math.random()
    }
});