'use strict';

/**
 * After applying this directive element.board will be chess board (chessboard.js)
 * and element.game will be chess game (chess.js)
 * Also drag and drop functions will be defined.
 * This small library is useful for separating drag&drop logic and application logic
 */
playzoneControllers.directive('chessBoardLegal', function (SettingService, $timeout, $rootScope) {
    function isMyTurn(scope, element) {
        return element.game.turn() === scope.game.color;
    }

    function doMoveOnTheBoard(scope, element, to) {
        scope.current_move.to = to; // move by click&click
        scope.current_move.promotion = 'q'; // NOTE: always promote to a queen for example simplicity

        if (!element.game.move(scope.current_move)) {
            scope.current_move.to = false;
            scope.pre_move = false;
            return;
        }

        scope.game.pgn = element.game.pgn();

        if (!element.board) {
            return;
        }

        element.board.position(element.game.fen());
        element.updateStatus();
        element.onMove(scope.current_move);
    }

    function moveByOneClick(element, square, scope) {
        if (!isMyTurn(scope, element)) {
            return;
        }

        var legalMoves = element.game.moves({verbose: true});

        var isSingleOptionToMoveHere = false;
        var isSingleOptionFromMoveHere = false;

        $.each(
            legalMoves,
            function (index, value) {
                if (value.to === square) {
                    if (isSingleOptionToMoveHere) {
                        isSingleOptionToMoveHere = false;
                        scope.current_move = false;
                        return false;
                    } else {
                        isSingleOptionToMoveHere = true;
                        scope.current_move = {
                            from: value.from
                        };
                    }
                } else if (value.from === square) {
                    if (isSingleOptionFromMoveHere) {
                        isSingleOptionFromMoveHere = false;
                        scope.current_move = false;
                        return false;
                    } else {
                        isSingleOptionFromMoveHere = true;
                        scope.current_move = {
                            from: square,
                            to: value.to
                        };
                    }
                }
            }
        );

        if (isSingleOptionToMoveHere) {
            doMoveOnTheBoard(scope, element, square);
            return true;
        } else if (isSingleOptionFromMoveHere) {
            doMoveOnTheBoard(scope, element, scope.current_move.to);
            return true;
        } else {
            return false;
        }
    }

    return {
        restrict: 'C',
        link: function(scope, element) {
            $rootScope.chess = element.game = new Chess();

            var statusEl = $(element).find('.status'),
                fenEl = $(element).find('.fen'),
                pgnEl = $(element).find('.pgn');

            var highlightSquare = function (square) {
                $(element).find('[class*="square"]').removeClass(scope.boardConfig.highlightClass);
                $(element).find('.square-' + square).addClass(scope.boardConfig.highlightClass);
            };

            // do not pick up pieces if the game is over
            // only pick up pieces for the side to move
            var onDragStart = function(source, piece, position, orientation) {
                if (scope.game.status !== 'play' || !scope.game.mine || !element.isMyMove(piece)) {
                    return false;
                }

                if (!scope.current_move) {
                    scope.current_move = { from: source }; // for move click&click
                } else {
                    doMoveOnTheBoard(scope, element, source);
                    return true;
                }

                return !scope.pre_move;
            };

            var onDrop = function(source, target) {
                if (!isMyTurn(scope, element)) {
                    // pre-move functionality for draggable
                    scope.pre_move = {from: source, to: target};

                    $timeout(
                        function () {
                            scope.highlightLastMove(scope, element, scope.pre_move);
                        },
                        150
                    );

                    return;
                }
                // see if the move is legal
                var moveObject = {
                    from: source,
                    to: target,
                    promotion: 'q' // NOTE: always promote to a queen for example simplicity
                };
                var move = element.game.move(moveObject);

                // illegal move
                if (move === null) return 'snapback';

                element.updateStatus();

                if (element.onMove) {
                    element.onMove(moveObject);
                }
            };

            $(element).on('click', '[class*="square"]', function () {
                if (!scope.game.mine) {
                    return false;
                }

                var square = $(this).data('square');

                var piece = $(this).find('img');

                var isMyPiece = piece.length && piece.data('piece').indexOf(scope.game.color) === 0;

                if (!scope.current_move &&
                    SettingService.getSetting('One-click move')) { // move by one click
                    if (moveByOneClick(element, square, scope)) {
                        return;
                    }
                }

                if (isMyTurn(scope, element)) {
                    if (!scope.current_move || isMyPiece) {
                        scope.current_move = {
                            from: square
                        };
                        highlightSquare(square);
                    } else {
                        doMoveOnTheBoard(scope, element, square);
                    }
                } else {
                    if (!scope.pre_move || !!scope.pre_move.to) {
                        scope.pre_move = {
                            from: square
                        };
                        highlightSquare(square);
                    } else {
                        scope.pre_move.to = square;
                        scope.highlightLastMove(scope, element, scope.pre_move);
                    }

                }
            });

            // update the board position after the piece snap
            // for castling, en passant, pawn promotion
            var onSnapEnd = function() {
                element.board.position(element.game.fen());
            };

            element.updateStatus = function() {
                var status = '';

                var moveColor = 'White';
                if (element.game.turn() === 'b') {
                    moveColor = 'Black';
                }

                // checkmate?
                if (element.game.in_checkmate() === true) {
                    status = 'Game over, ' + moveColor + ' is in checkmate.';
                }

                // draw?
                else if (element.game.in_draw() === true) {
                    status = 'Game over, drawn position';
                }

                // game still on
                else {
                    status = moveColor + ' to move';

                    // check?
                    if (element.game.in_check() === true) {
                        status += ', ' + moveColor + ' is in check';
                    }
                }

                statusEl.html(status);
                fenEl.html(element.game.fen());
            };

            element.loadBoard = function (userConfig) {
                if (!$('#' + element.data('board')).length) {
                    return;
                }

                $rootScope.chessBoard = element.board = ChessBoard(element.data('board'), {
                    draggable: scope.boardConfig.draggable,
                    moveSpeed: 1,
                    position: 'start',
                    onDragStart: onDragStart,
                    onDrop: onDrop,
                    onSnapEnd: onSnapEnd,
                    pieceTheme: 'img/chesspieces/' + userConfig.pieceType + '/{piece}.png'
                });
                if (scope.game.color === 'b') {
                    element.board.flip();
                }
                element.updateStatus();
            };

            var rtime;
            var timeout = false;
            var delta = 200;
            $(window).resize(function() {
                rtime = new Date();
                if (timeout === false) {
                    timeout = true;
                    setTimeout(resizeend, delta);
                }
            });

            function resizeend() {
                if (new Date() - rtime < delta) {
                    setTimeout(resizeend, delta);
                } else {
                    timeout = false;
                    element.loadBoard(scope.boardConfig);
                    element.loadPgn(scope.game.pgn);
                }
            }

            element.loadPgn = function (pgn) {
                element.game.load_pgn(pgn);
                element.board.position(element.game.fen());
                element.updateStatus();
            };
        }
    }
});