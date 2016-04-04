'use strict';

/**
 * After applying this directive element.board will be chess board (chessboard.js)
 * and element.game will be chess game (chess.js)
 * Also drag and drop functions will be defined.
 * This small library is useful for separating drag&drop logic and application logic
 */
playzoneControllers.directive('chessBoardLegal', function () {
    function doMoveOnTheBoard(scope, element, to) {
        scope.current_move.to = to; // move by click&click

        if (!element.game.move(scope.current_move)) {
            scope.current_move = scope.pre_move = false;
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

    return {
        restrict: 'C',
        link: function(scope, element) {
            element.game = new Chess();

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
                if (scope.game.status !== 'play') {
                    return false;
                }

                if (!scope.current_move) {
                    scope.current_move = { from: source }; // for move click&click
                } else {
                    doMoveOnTheBoard(scope, element, source);
                    return true;
                }

                if (!scope.pre_move) {
                    return true;
                }
                if (element.onDragStart && !element.onDragStart()) {
                    return false;
                }
                if (element.game.game_over() === true ||
                    (element.game.turn() === 'w' && piece.search(/^b/) !== -1) ||
                    (element.game.turn() === 'b' && piece.search(/^w/) !== -1)) {
                    return false;
                }
            };

            var onDrop = function(source, target) {
                if (element.game.turn() !== scope.game.color) {
                    scope.pre_move = {from: source, to: target};
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
                if (element.onDragStart && !element.onDragStart()) {
                    return false;
                }

                var square = $(this).data('square');
                
                if (!scope.current_move) {
                    scope.current_move = { from: square };
                    highlightSquare(square);
                    return;
                }

                doMoveOnTheBoard(scope, element, square);
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
                pgnEl.html(element.game.pgn());
            };

            element.loadBoard = function (userConfig) {
                element.board = ChessBoard(element.data('board'), {
                    draggable: scope.boardConfig.draggable,
                    moveSpeed: 1,
                    position: 'start',
                    onDragStart: onDragStart,
                    onDrop: onDrop,
                    onSnapEnd: onSnapEnd,
                    pieceTheme: 'img/chesspieces/' + userConfig.pieceType + '/{piece}.png'
                });
                element.updateStatus();
            };

            element.loadPgn = function (pgn) {
                element.game.load_pgn(pgn);
                element.board.position(element.game.fen());
                element.updateStatus();
            };
        }
    }
});