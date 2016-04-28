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
playzoneControllers.directive('playChessBoard', function (WebRTCService, WebsocketService, AudioService, $timeout, $rootScope) {
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
            scope.highlightLastMove = function (scope, element, lastMove) {
                $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);
                var history = element.game.history({verbose: true});

                !lastMove && (lastMove = history[history.length - 1]);

                $(element).find('.square-' + lastMove.from).addClass(scope.boardConfig.highlightClass);
                $(element).find('.square-' + lastMove.to).addClass(scope.boardConfig.highlightClass);
            };

            if ($rootScope.robot && !!$rootScope.robotGame) {
                $timeout(
                    function () {
                        scope.game = {
                            color: ["w", "b"][Math.floor(Math.random() * 2)],
                            status: "play",
                            mine: true
                        };

                        element.loadBoard(scope.boardConfig);

                        if (scope.game.color === 'b') {
                            element.board.flip();
                            scope.savePgnAndSendToObservers(
                                true,
                                window.btoa(
                                    "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1"
                                )
                            );
                        }

                        element.updateStatus();
                    },
                    1000
                );

                WebsocketService.addListener(
                    "listen_robot_move_" + scope.game.id,
                    "send_move_from_robot",
                    function(data) {
                        var from = data.move.substr(0,2);
                        var to = data.move.substr(2,2);
                        element.game.move({
                            from: from,
                            to: to,
                            promotion: "q"
                        });
                        scope.game.move_color = scope.game.move_color === 'w' ? 'b' : 'w';
                        scope.game.pgn = element.game.pgn();
                        element.board.position(element.game.fen());

                        AudioService.move();
                        element.updateStatus();
                        element.game.game_over() && scope.game.$get();

                        scope.highlightLastMove(scope, element);
                        makePreMoveIfExists(scope, element);
                    });
            }

            !$rootScope.robot && scope.game.$promise.then(
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

                                    if (scope.game.mine && scope.game.status === 'end') {
                                        // it means that opponent has resigned or draw
                                        scope.game.result_white != '0.5' ?
                                            AudioService.win() : AudioService.draw();
                                    }
                                }
                            );
                            return;
                        }

                        var receivedPgn = window.atob(data.encoded_pgn);

                        if (receivedPgn.length <= scope.game.pgn.length) {
                            return;
                        }

                        scope.game.move_color = scope.game.move_color === 'w' ? 'b' : 'w';
                        scope.game.time_black = data.time_black;

                        scope.game.time_white = data.time_white;

                        if (receivedPgn.length <= scope.game.pgn.length) {
                            scope.highlightLastMove(scope, element);
                            makePreMoveIfExists(scope, element);
                            return;
                        }

                        scope.game.pgn = receivedPgn;
                        element.game.load_pgn(receivedPgn);
                        AudioService.move();
                        element.board.position(element.game.fen());
                        element.updateStatus();
                        element.game.game_over() && scope.game.$get();

                        scope.highlightLastMove(scope, element);
                        makePreMoveIfExists(scope, element);
                    });
                }
            );

            element.onDragStart = function (source) {
                return true;
            };

            element.isMyMove = function (piece) {
                return !piece || piece.indexOf(scope.game.color) === 0;
            };

            element.onMove = function (move) {
                scope.current_move = scope.pre_move = false;
                $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);

                scope.game.pgn = element.game.pgn();

                if (!scope.game.id) { // isRobot
                    scope.savePgnAndSendToObservers(true, window.btoa(element.game.fen()));
                } else {
                    scope.savePgnAndSendToObservers(true);
                }

                element.game.game_over() && !element.game.in_checkmate() && scope.draw();
                element.game.in_checkmate() && AudioService.win();
                scope.highlightLastMove(scope, element);

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


