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
playzoneControllers.directive('playChessBoard', function (WebRTCService, WebsocketService) {
    return {
        restrict: 'C',
        link: function(scope, element) {
            scope.game.$promise.then(
                function() {
                    element.loadBoard(scope.boardConfig);
                    element.loadPgn(scope.game.pgn);

                    if (scope.game.color === 'b') {
                        element.board.flip();
                    }

                    WebsocketService.addListener("listen_game_" + scope.game.id, "game_pgn_" + scope.game.id, function(data) {
                        if (!data.encoded_pgn) { // it means that game is finished
                            scope.game.$get();
                            return;
                        }

                        var receivedPgn = window.atob(data.encoded_pgn);

                        if (receivedPgn.length <= scope.game.pgn.length) {
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
            );

            element.onDragStart = function () {
                return scope.game.status === 'play' && element.game.turn() === scope.game.color;
            };

            element.onMove = function (move) {
                WebRTCService.sendMessage({
                    gameId: scope.game.id,
                    move: move
                });
                scope.game.pgn = element.game.pgn();
                scope.savePgnAndTime();

                WebsocketService.sendGameToObservers(
                    scope.game.id,
                    window.btoa(scope.game.pgn)
                );

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
                    if (scope.game.status !== 'play') {
                        return;
                    }
                    console.log('webRTCMessage', webRTCMessage);
                    if (!webRTCMessage.gameId || webRTCMessage.gameId !== scope.game.id) {
                        return;
                    }

                    if (webRTCMessage.draw || webRTCMessage.resign) {
                        return;
                    }

                    element.game.move(webRTCMessage.move);

                    scope.game.pgn = element.game.pgn();

                    if (!element.board) {
                        return;
                    }

                    element.board.position(element.game.fen());
                    element.updateStatus();

                    if (element.game.game_over()) {
                        switch (true) {
                            case element.game.in_checkmate():
                                console.log(webRTCMessage);
                                scope.resign();
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


