'use strict';

/**
 * Application logic of chessboard. This directive uses element.board and element.game
 * which were defined in chess-board-legal library and scope.game which should be defined in
 * controller scope. Otherwise we will get error (undefined scope.game).
 *
 * Important to note:
 * scope.game - application angular rest model with information about current game.
 * element.game - chess.js plugin (with pgn functions etc.)
 * element.board - chessboard.js plugin (without move validation, just board interface)
 */
playzoneControllers.directive('playChessBoard', function (WebRTCService, ChessLocalStorageService) {
    return {
        restrict: 'C',
        link: function(scope, element) {
            scope.game.$promise.then(
                function() {
                    element.loadBoard(scope.boardConfig);
                    var localStoredPgn = ChessLocalStorageService.getPgn(scope.game.id);
                    element.loadPgn(localStoredPgn ? localStoredPgn : scope.game.pgn);

                    if (scope.game.color === 'b') {
                        element.board.flip();
                    }
                }
            );

            element.onDragStart = function () {
                return element.game.turn() === scope.game.color;
            };

            element.onMove = function (move) {
                WebRTCService.sendMessage(move);
            };

            WebRTCService.addMessageListener(
                function (move) {
                    element.game.move(move);
                    element.board.position(element.game.fen());
                    ChessLocalStorageService.setPgn(scope.game.id, element.game.pgn());
                    element.updateStatus();
                }
            );
        }
    };
});


