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
    var highlightLastMove = function (scope, element) {
        $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);
        var history = element.game.history({verbose: true});
        var lastMove = history[history.length - 1];
        $(element).find('.square-' + lastMove.from).addClass(scope.boardConfig.highlightClass);
        $(element).find('.square-' + lastMove.to).addClass(scope.boardConfig.highlightClass);
    };

    var makePreMoveIfExists = function (scope, element) {
        if (!scope.game.mine || !scope.pre_move) { //premove
            return;
        }

        if (!element.game.move(scope.pre_move)) {
            scope.pre_move = false;
            return;
        }

        scope.game.pgn = element.game.pgn();

        if (!element.board) {
            return;
        }

        element.board.position(element.game.fen());
        element.updateStatus();
        element.onMove(scope.pre_move);
        scope.pre_move = false;
    };

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
                        if (!data.encoded_pgn && scope.game.status === 'play') {
                            // it means that game is finished or drawn and we have to fix result
                            scope.game.$get().then( // get game from server
                                function () {
                                    scope.game.mine && element.game.game_over() &&
                                    !element.game.in_checkmate() && scope.draw(); // fix draw
                                }
                            );
                            return;
                        }

                        scope.game.move_color = scope.game.move_color === 'w' ? 'b' : 'w';

                        if (data.color === 'w') {
                            scope.game.time_black = data.time;
                        } else {
                            scope.game.time_white = data.time;
                        }

                        var receivedPgn = window.atob(data.encoded_pgn);

                        if (receivedPgn.length <= scope.game.pgn.length) {
                            highlightLastMove(scope, element);
                            makePreMoveIfExists(scope, element);
                            return;
                        }

                        scope.game.pgn = receivedPgn;
                        element.game.load_pgn(receivedPgn);
                        element.board.position(element.game.fen());
                        element.updateStatus();

                        highlightLastMove(scope, element);
                        makePreMoveIfExists(scope, element);
                    });
                }
            );

            element.onDragStart = function (source) {
                return scope.game.status === 'play' && element.game.turn() === scope.game.color;
            };

            element.onMove = function (move) {
                scope.current_move = scope.pre_move = false;
                $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);

                scope.game.pgn = element.game.pgn();
                scope.savePgnAndSendToObservers(true);

                element.game.game_over() && !element.game.in_checkmate() && scope.draw();

                WebRTCService.sendMessage({
                    gameId: scope.game.id,
                    move: move
                });
            };

            WebRTCService.addMessageListener(
                function (webRTCMessage) {
                    if (scope.game.status !== 'play' || !webRTCMessage.gameId || webRTCMessage.gameId !== scope.game.id) {
                        return;
                    }

                    element.game.move(webRTCMessage.move);

                    scope.game.pgn = element.game.pgn();

                    if (!element.board) {
                        return;
                    }

                    element.board.position(element.game.fen());
                    element.updateStatus();
                },
                'move'
            );
        }
    };
});


