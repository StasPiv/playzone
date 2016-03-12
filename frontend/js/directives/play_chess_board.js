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
playzoneControllers.directive('playChessBoard', function (WebRTCService, ChessLocalStorageService, WebsocketService) {
    return {
        restrict: 'C',
        link: function(scope, element) {
            scope.game.$promise.then(
                function() {
                    element.loadBoard(scope.boardConfig);
                    var localStoredPgn = ChessLocalStorageService.getPgn(scope.game.id);

                    if (!localStoredPgn || localStoredPgn.length < scope.game.pgn.length) {
                        ChessLocalStorageService.setPgn(scope.game.id, scope.game.pgn);
                        localStoredPgn = scope.game.pgn;
                    }

                    element.loadPgn(localStoredPgn);

                    if (scope.game.color === 'b') {
                        element.board.flip();
                    }

                    if (!scope.game.color) {
                        WebsocketService.addListener("listen_game_" + scope.game.id, "game_pgn_" + scope.game.id, function(data) {
                            console.log('listener should do move');
                            var receivedPgn = window.atob(data.encoded_pgn);
                            console.log(receivedPgn);

                            if (receivedPgn.length < scope.game.pgn.length) {
                                return;
                            }

                            scope.game.pgn = receivedPgn;
                            element.game.load_pgn(receivedPgn);
                            element.board.position(element.game.fen());
                            element.updateStatus();

                            if (element.game.game_over()) {
                                setTimeout(
                                    function () {
                                        scope.game.$get();
                                    },
                                    1500
                                );
                            }
                        });
                    }
                }
            );

            element.onDragStart = function () {
                return scope.game.status == 'play' && element.game.turn() === scope.game.color;
            };

            element.onMove = function (move) {
                WebRTCService.sendMessage({
                    gameId: scope.game.id,
                    move: move
                });
                ChessLocalStorageService.setPgn(scope.game.id, element.game.pgn());
                scope.game.pgn = element.game.pgn();
                scope.game.$savePgn();
                WebsocketService.sendGameToObservers(scope.game.id, window.btoa(scope.game.pgn));

                if (element.game.game_over()) {
                    switch (true) {
                        case element.game.in_checkmate():
                            break;
                        default:
                            scope.game.$offerDraw();
                            break;
                    }

                    setTimeout(
                        function () {
                            scope.game.$get();
                        },
                        1500
                    );
                }
            };

            WebRTCService.addMessageListener(
                function (webRTCMessage) {
                    console.log('webRTCMessage', webRTCMessage);
                    if (!webRTCMessage.gameId || webRTCMessage.gameId !== scope.game.id) {
                        return;
                    }

                    if (webRTCMessage.draw) {
                        return;
                    }

                    element.game.move(webRTCMessage.move);
                    scope.game.pgn = element.game.pgn();
                    ChessLocalStorageService.setPgn(scope.game.id, element.game.pgn());

                    if (!element.board) {
                        return;
                    }

                    element.board.position(element.game.fen());
                    element.updateStatus();

                    if (element.game.game_over()) {
                        switch (true) {
                            case element.game.in_checkmate():
                                scope.game.$resign();
                                break;
                            default:
                                scope.game.$acceptDraw();
                                break;
                        }
                    }
                },
                'move'
            );
        }
    };
});


