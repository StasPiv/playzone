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
playzoneControllers.directive('playChessBoard', function (WebRTCService, WebsocketService, AudioService, $timeout, GameRest) {
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
                    scope.game.insufficient_material_white = insufficient_material_white(element.game.fen());
                    scope.game.insufficient_material_black = insufficient_material_black(element.game.fen());

                    if (
                        scope.game.opponent && scope.game.opponent.login === 'Robot' &&
                        scope.game.move_color !== scope.game.color
                    ) {

                        GameRest.getRobotmove(
                            {
                                encoded_fen: window.btoa(element.game.fen()),
                                id: scope.game.id
                            },
                            function (data) {
                                element.game.move({
                                    from: data.from,
                                    to: data.to,
                                    promotion: "q"
                                });
                                scope.game.pgn = element.game.pgn();

                                scope.savePgnAndSendToObservers(true);

                                element.board.position(element.game.fen());

                                element.game.game_over() && !element.game.in_checkmate() && scope.draw();

                                AudioService.move();
                                element.updateStatus();

                                element.game.game_over() && scope.game.$get();
                                scope.highlightLastMove(scope, element);

                                makePreMoveIfExists(scope, element);
                            }
                        );
                    }

                    WebsocketService.addListener("listen_game_" + scope.game.id, "game_pgn_" + scope.game.id, function(data) {
                        if (!data.encoded_pgn && scope.game.status === 'play') {
                            // it means that game is finished or drawn and we have to fix result
                            scope.game.$get().then( // get game from server
                                function () {
                                    scope.game.mine &&
                                    (element.game.game_over() && !element.game.in_checkmate() ||
                                    element.game.insufficient_material()) &&
                                    scope.draw(); // fix draw

                                    if (scope.game.mine && scope.game.status === 'end') {
                                        // it means that opponent has resigned or draw
                                        scope.game.my_result == '1' ?
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
                        var date = new Date();
                        console.log("latency", date.getTime() - data.milliseconds);
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
                var date = new Date();

                console.log("send", date.getTime());
                scope.current_move = scope.pre_move = false;
                $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);

                scope.game.pgn = element.game.pgn();
                console.log('fen after move', element.game.fen());
                scope.game.insufficient_material_white = insufficient_material_white(element.game.fen());
                scope.game.insufficient_material_black = insufficient_material_black(element.game.fen());

                if (scope.game.opponent.login === "Robot") { // isRobot
                    scope.savePgnAndSendToObservers(true);
                    GameRest.getRobotmove(
                        {
                            encoded_fen: window.btoa(element.game.fen()),
                            id: scope.game.id
                        },
                        function (data) {
                            element.game.move({
                                from: data.from,
                                to: data.to,
                                promotion: "q"
                            });
                            scope.game.pgn = element.game.pgn();

                            scope.savePgnAndSendToObservers(true);

                            element.board.position(element.game.fen());
                            AudioService.move();
                            element.updateStatus();

                            element.game.game_over() && scope.game.$get();
                            scope.highlightLastMove(scope, element);

                            makePreMoveIfExists(scope, element);
                        }
                    );
                } else {
                    scope.savePgnAndSendToObservers(true);
                }

                (element.game.game_over() && !element.game.in_checkmate() ||
                    element.game.insufficient_material())
                && scope.draw();
                element.game.in_checkmate() && AudioService.win();
                scope.highlightLastMove(scope, element);

                var dateRTC = new Date();
                WebRTCService.sendMessage({
                    gameId: scope.game.id,
                    move: move,
                    ms: dateRTC.getTime()
                });
            };

            WebRTCService.addMessageListener(
                function (webRTCMessage) {
                    if (scope.game.status !== 'play' || !webRTCMessage.gameId || webRTCMessage.gameId !== scope.game.id) {
                        return;
                    }

                    var date = new Date();
                    console.log("latency webRTC", date.getTime() - webRTCMessage.ms);

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


