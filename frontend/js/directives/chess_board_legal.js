'use strict';

/**
 * After applying this directive element.board will be chess board (chessboard.js)
 * and element.game will be chess game (chess.js)
 * Also drag and drop functions will be defined.
 * This small library is useful for separating drag&drop logic and application logic
 */
playzoneControllers.directive('chessBoardLegal', function () {
    return {
        restrict: 'C',
        link: function(scope, element) {
            element.game = new Chess();

            var statusEl = $(element).find('.status'),
                fenEl = $(element).find('.fen'),
                pgnEl = $(element).find('.pgn');

            // do not pick up pieces if the game is over
            // only pick up pieces for the side to move
            var onDragStart = function(source, piece, position, orientation) {
                if (element.game.game_over() === true ||
                    (element.game.turn() === 'w' && piece.search(/^b/) !== -1) ||
                    (element.game.turn() === 'b' && piece.search(/^w/) !== -1)) {
                    return false;
                }
            };

            var onDrop = function(source, target) {
                // see if the move is legal
                var move = element.game.move({
                    from: source,
                    to: target,
                    promotion: 'q' // NOTE: always promote to a queen for example simplicity
                });

                // illegal move
                if (move === null) return 'snapback';

                element.updateStatus();
            };

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
                    draggable: true,
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

            //element.loadBoard({pieceType:'merida'})
        }
    }
});