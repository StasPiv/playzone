/**
 * Created by stas on 06.03.16.
 */
'use strict';

playzoneControllers.controller('PlayActionCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService) {
    $scope.resign = function () {
        $scope.game.$resign().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    resign: true
                });
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.draw = function () {
        if ($scope.opponentOfferDraw) {
            $scope.game.$acceptDraw().then(
                function () {
                    WebRTCService.sendMessage({
                        gameId: $scope.game.id,
                        draw: 'accept'
                    });
                    WebsocketService.sendGameToObservers($scope.game.id);
                }
            );
            return;
        }

        $scope.game.$offerDraw().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    draw: 'offer'
                });
            }
        );
    };

    WebRTCService.addMessageListener(
        function (webRTCMessage) {
            $scope.opponentOfferDraw = false;
            if (!webRTCMessage.gameId || webRTCMessage.gameId !== $scope.game.id || !webRTCMessage.draw) {
                return;
            }

            switch (webRTCMessage.draw) {
                case 'offer':
                    $scope.opponentOfferDraw = true;
                    break;
                case 'accept':
                    $scope.game.$get();
                    break;
            }
        },
        'draw'
    );

    WebRTCService.addMessageListener(
        function (webRTCMessage) {
            if (!webRTCMessage.gameId || webRTCMessage.gameId !== $scope.game.id || !webRTCMessage.resign) {
                return;
            }

            $scope.game.$get();
        },
        'resign'
    );
});