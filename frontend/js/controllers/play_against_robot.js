/**
 * Created by stas on 27.04.16.
 */
'use strict';

playzoneControllers.controller('PlayAgainstRobotCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, EnvService, AudioService, SettingService, $location) {
    $scope.boardConfig = {
        pieceType: SettingService.getSetting('Piece type') ?
            SettingService.getSetting('Piece type') : 'leipzig',
        highlightClass: 'highlight1-32417',
        draggable: SettingService.getSetting('Draggable disabled') != 1
    };

    if (!$rootScope.robotGame) {
        $location.path("/");
    }
    
    $scope.game = {};
    $rootScope.robot = true;

    $scope.resign = function () {
        $location.path("/");
    };

    $scope.draw = function () {

    };

    $scope.sendWithWebsockets = function () {
        if ($scope.game.status === 'play') {
            WebsocketService.sendGameToObservers(
                $scope.game.id,
                window.btoa($scope.game.pgn),
                $scope.game.time_white,
                $scope.game.time_black,
                $scope.game.color
            );
        } else {
            WebsocketService.sendGameToObservers($scope.game.id);
        }
    };

    $scope.savePgnAndSendToObservers = function (withoutSaving, encodedFenForRobot) {
        if (encodedFenForRobot) {
            WebsocketService.sendFenToRobot($scope.game.id, encodedFenForRobot);
            return;
        }

        if (withoutSaving) {
            $scope.sendWithWebsockets();
        }
    };

});