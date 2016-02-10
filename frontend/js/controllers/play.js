/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope) {
    $scope.boardSettings = {
        boardName: 'playzone',
        pieceSet: 'leipzig',
        pieceSize: '46',
        pgnString: '1.e4 e5',
        reverseFlip: false,
        pauseBetweenMoves: '2000',
        showCoordinates: true,
        clickAndClick: true
    };
});