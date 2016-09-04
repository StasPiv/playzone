/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest, WebRTCService, WebsocketService, EnvService, AudioService, SettingService, ChatRest, $timeout, CallRest, $location) {
    $scope.chat = ChatRest.query();

    $scope.getBoardConfig = function() {
        return {
            pieceType: SettingService.getSetting('Piece type') ?
                SettingService.getSetting('Piece type') : 'merida',
            highlightClass: 'highlight1-32417',
            draggable: !$rootScope.isMobile && SettingService.getSetting('Draggable disabled') != 1,
            showNotation: !!SettingService.getSetting('Show notation')
        };
    };

    $scope.boardConfig = $scope.getBoardConfig();

    $scope.gameConfig = {
        zeitnotLimit: SettingService.getSetting('Zeitnot limit') ?
                      SettingService.getSetting('Zeitnot limit') * 1000 :
                      28000
    };

    $rootScope.game = $scope.game = GameRest.get(
        {
            id: $routeParams.gameId
        }
    );

    $scope.game.$promise.then(
        function () {
            WebsocketService.subscribeToGame($scope.game.id);
            
            if (true || !EnvService.isWebRTC()) { // TODO: need to remove "true" for webRTC support
                return;
            }

            switch ($scope.game.color) {
                case 'w':
                    WebRTCService.createGameRoom($scope.game.id);
                    break;
                case 'b':
                    WebRTCService.joinGameRoom($scope.game.id);
                    WebRTCService.addCallBackLeaveRoom($scope.game.id, function () {
                        $scope.game.$savePgn();
                    });
                    break;
            }
        }
    );

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
        $scope.opponentOfferDraw =
            $scope.game.draw && $scope.game.draw !== $scope.game.color;

        if ($scope.opponentOfferDraw) {
            GameRest.acceptDraw(
                {
                    id: $scope.game.id
                },
                function () {
                    WebsocketService.sendGameToObservers($scope.game.id);
                    AudioService.draw();
                    $scope.game.$get();
                }
            );
            return;
        }

        GameRest.offerDraw(
            {
                id: $scope.game.id
            },
            function () {
                WebsocketService.sendGameToObservers($scope.game.id);
            }
        );
    };

    $scope.sendWithWebsockets = function () {
        console.log('sendWithWS');
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

    $scope.savePgnAndSendToObservers = function (withoutSaving, move, moveNumber, fen) {
        if (!$scope.game.mine) {
            return;
        }

        if (withoutSaving) {
            var incrementWhite = ($scope.game.color === 'b') ? $scope.game.game_params.time_increment : 0;
            var incrementBlack = ($scope.game.color === 'w') ? $scope.game.game_params.time_increment : 0;

            $scope.game.current_move = moveNumber;
            $scope.game.time_white += incrementWhite;
            $scope.game.time_black += incrementBlack;

            $scope.game.move_color = $scope.game.move_color === 'w' ? 'b' : 'w';

            if (move) {
                WebsocketService.sendMoveToObservers(
                    $scope.game.id,
                    move,
                    $scope.game.time_white,
                    $scope.game.time_black,
                    $scope.game.move_color,
                    moveNumber,
                    fen
                );
            } else {
                $scope.sendWithWebsockets();
            }
            
        }

        $timeout(
            function () {
                $scope.game.$savePgn().then(
                    function () {
                        $scope.game.opponent.offline = !$rootScope.loginsOnline.searchById($scope.game.opponent.id);
                        if (!withoutSaving || $scope.game.status == 'end') {
                            $scope.sendWithWebsockets();
                        }
                    }
                );
            },
            0
        );
    };

    $scope.publishPgn = function () {
        $scope.game.$publishPgn().then(
            function () {
                $scope.publishLink = "http://immortalchess.net/forum/showthread.php?t=31003&goto=newpost";
            }
        );
    };

    $scope.offerRevenge = function () {
        CallRest.send(
            {},
            {
                player: $scope.game.opponent.login,
                time: {
                    base_minutes: $scope.game.game_params.time_base / 60000,
                    increment_seconds: $scope.game.game_params.time_increment / 1000
                },
                rate: !!$scope.game.game_params.rate
            },
            function(responseCall) {
                WebsocketService.sendDataToLogins(
                    'offer_revenge', responseCall, [$scope.game.opponent.login]
                );
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };

    $scope.acceptRevenge = function() {
        CallRest.accept({},$scope.revengeCall, function(responseGame) {
            WebsocketService.sendDataToLogins(
                'accept_revenge',
                {
                    game_id: responseGame.id,
                    call_id: $scope.revengeCall.id
                },
                []
            );
            AudioService.newGame();
            $location.path( '/play/' + responseGame.id );
        });
    };

    $scope.abort = function () {
        $scope.game.$abort().then(
            function () {
                $scope.sendWithWebsockets();
            }
        );
    };

    $scope.highlightLastMove = highlightLastMove;

    WebsocketService.addListener('listen_opponent_gone', 'user_gone', function (user) {
        if ($scope.game.opponent && user['login'] === $scope.game.opponent.login) {
            console.log('opponent has gone');
            $scope.savePgnAndSendToObservers();
            $scope.game.opponent.offline = true;
        }
    });

    WebsocketService.addListener('listen_opponent_in', 'user_in', function (user) {
        if ($scope.game && $scope.game.opponent && user['login'] === $scope.game.opponent.login) {
            console.log('opponent has reconnected');
            $scope.game.opponent.offline = false;
        }
    });

    $scope.revengeOffered = false;
    WebsocketService.addListener('offer_revenge', 'offer_revenge', function (call) {
        $scope.revengeCall = call;
        $scope.revengeOffered = true;
    });

    WebsocketService.addListener('accept_revenge', 'accept_revenge', function (data) {
        AudioService.newGame();
        $location.path( '/play/' + data.game_id );
    });

    WebsocketService.addListener("listen_accepted_calls", "call_accept", function(data) {
        if (data.game.mine) {
            console.log(data.game);
            $location.path( '/play/' + data.game.id );
            AudioService.newGame();
        }
    });
});