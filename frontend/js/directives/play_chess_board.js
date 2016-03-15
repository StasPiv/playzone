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
playzoneControllers.directive('playChessBoard', function (WebRTCService, ChessLocalStorageService, WebsocketService, $interval) {
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
                return scope.game.status === 'play' && element.game.turn() === scope.game.color;
            };

            element.onMove = function (move) {
                WebRTCService.sendMessage({
                    gameId: scope.game.id,
                    move: move,
                    my_time: scope.my_time,
                    opponent_time: scope.opponent_time
                });
                ChessLocalStorageService.setPgn(scope.game.id, element.game.pgn());
                scope.game.pgn = element.game.pgn();
                scope.savePgnAndTime();
                WebsocketService.sendGameToObservers(scope.game.id, window.btoa(scope.game.pgn));

                scope.my_move = false;

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

                    scope.my_move = true;

                    element.game.move(webRTCMessage.move);

                    // synchronize times (no lag compensation here)
                    webRTCMessage.opponent_time && (scope.my_time = webRTCMessage.opponent_time);

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
                                scope.resign();
                                break;
                            default:
                                scope.game.$acceptDraw();
                                break;
                        }
                    }

                    if (scope.game.my_time < 0) {
                        scope.resign();
                    }
                },
                'move'
            );
        }
    };
});


