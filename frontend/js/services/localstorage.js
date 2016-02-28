/**
 * Created by stas on 28.02.16.
 */
'use strict';

playzoneServices.factory('ChessLocalStorageService', function(localStorageService) {
    return {
        getGameKey: function (gameId) {
            return 'game' + gameId;
        },
        getPgn: function (gameId) {
            return localStorageService.get(this.getGameKey(gameId));
        },
        setPgn: function (gameId, pgn) {
            localStorageService.set(this.getGameKey(gameId), pgn);
        }
    };
});