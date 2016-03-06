/**
 * Created by stas on 06.03.16.
 */
'use strict';

playzoneControllers.controller('PlayActionCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, $translate, $location) {
    $scope.resign = function () {
        $scope.game.$resign().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    resign: true
                });
            }
        );
    };

    $scope.allowOfferDraw = true;

    $scope.draw = function () {
        if (!$scope.allowOfferDraw) {
            return;
        }

        $scope.game.$offerDraw().then(
            function () {
                WebRTCService.sendMessage({
                    gameId: $scope.game.id,
                    draw: "offer"
                });
                $scope.allowOfferDraw = false; // to prevent "draw annoying" popups for opponent
            }
        );
    };

    WebRTCService.addMessageListener(
        function (webRTCMessage) {
            if (!webRTCMessage.gameId || webRTCMessage.gameId !== $scope.game.id || !webRTCMessage.draw) {
                return;
            }

            switch (webRTCMessage.draw) {
                case 'offer':
                    $scope.allowOfferDraw = true;
                    $translate(['Draw']).then(function (translations) {
                        var acceptDraw = window.confirm(translations.Draw + "?");
                        if (acceptDraw) {
                            $scope.game.$acceptDraw().then(
                                function () {
                                    WebRTCService.sendMessage({
                                        gameId: $scope.game.id,
                                        draw: "accept"
                                    });
                                    window.alert(translations.Draw + "!");
                                }
                            );
                        }
                    });
                    break;
                case 'accept':
                    $translate(['Draw']).then(function (translations) {
                        window.alert(translations.Draw + "!");
                    });
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

            $translate(["Opponent has been resigned. Congratulations!"]).then(function (translations) {
                window.alert(translations["Opponent has been resigned. Congratulations!"]);
            });
        },
        'resign'
    );
});