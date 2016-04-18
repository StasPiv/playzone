/**
 * Created by stas on 13.04.16.
 */
'use strict';

playzoneServices.factory('AudioService', function(SettingService) {
    var play = function (audioFile) {
        var audio = new Audio('audio/Board/' + audioFile);
        audio.play();
    };

    function getRandomArbitrary(min, max) {
        return parseInt(Math.random() * (max - min) + min);
    }

    return {
        newCall: function () {
            SettingService.getSetting('Sound call') == 1 &&
            play('Server/Gong.wav');
        },
        newGame: function () {
            SettingService.getSetting('Sound new game') == 1 &&
            play('NEWGAME.WAV');
        },
        win: function () {
            SettingService.getSetting('Sound win') == 1 &&
            play('Server/WinApplause' + getRandomArbitrary(1,5) + '.wav');
        },
        draw: function () {
            SettingService.getSetting('Sound draw') == 1 &&
            play('Server/Clapping.wav');
        },
        move: function () {
            SettingService.getSetting('Sound move') == 1 &&
            play('MOVE' + getRandomArbitrary(1,6) + '.WAV');
        },
        capture: function () {
            play('CAPTURE' + getRandomArbitrary(1,5) + '.WAV');
        }
    };
});