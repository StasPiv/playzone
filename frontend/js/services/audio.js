/**
 * Created by stas on 13.04.16.
 */
'use strict';

playzoneServices.factory('AudioService', function($cookies) {
    var play = function (audioFile) {
        var audio = new Audio('audio/' + audioFile);
        audio.play();
    };

    function getRandomArbitrary(min, max) {
        return parseInt(Math.random() * (max - min) + min);
    }

    return {
        newCall: function () {
            play('Board/Server/Gong.wav');
        },
        newGame: function () {
            play('Board/NEWGAME.WAV');
        },
        win: function () {
            play('Board/Server/WinApplause' + getRandomArbitrary(1,5) + '.wav');
        }
    };
});