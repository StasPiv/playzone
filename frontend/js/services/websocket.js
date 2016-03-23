/**
 * Created by stas on 24.01.16.
 */
'use strict';

playzoneServices.factory('WebsocketService', function($websocket, $location) {
    var listenersMap = {};
    // Open a WebSocket connection
    var webSocketPath = 'ws://ws.' + $location.host() + ':8081/';
    var dataStream = $websocket(webSocketPath);

    dataStream.onMessage(
        function(message) {
            console.log('test', message);
            var receivedMessage = angular.fromJson(message.data);

            if (!receivedMessage.method || !receivedMessage.data) {
                return;
            }

            if (!listenersMap[receivedMessage.method]) {
                return;
            }

            angular.forEach(listenersMap[receivedMessage.method], function (callback) {
                callback(receivedMessage.data);
            });
        }
    );

    return {
        addListener: function(listenerName, methodToListen, callback) {
            if (typeof callback !== 'function') {
                return;
            }
            if (!listenersMap[methodToListen]) {
                listenersMap[methodToListen] = {};
            }
            listenersMap[methodToListen][listenerName] = callback;
        },
        send: function(data) {
            console.log(data);
            var dataToSend = angular.toJson(data);
            console.log(dataToSend);
            dataStream.send(dataToSend);
        },
        introduction: function(user) {
            this.send(
                {
                    "scope": "introduction",
                    "method": "introduction",
                    "data": {
                        "login": user.login,
                        "token": user.token
                    }
                }
            );
        },
        sendDataToLogins: function(method, data, logins) {
            this.send(
                {
                    scope: 'send_to_users',
                    method: method,
                    logins: logins,
                    data: data
                }
            )
        },
        subscribeToGame: function(gameId) {
            console.log('subscribeToGame');
            this.send(
                {
                    scope: 'subscribe_to_game',
                    method: 'subscribe_to_game',
                    data: {
                        game_id: gameId
                    }
                }
            )
        },
        /**
         * Pass only first param (gameId) to fix result on observers' side
         *
         * @param gameId
         * @param encodedPgn
         * @param timeWhite
         * @param timeBlack
         * @param my_move
         */
        sendGameToObservers: function(gameId, encodedPgn, timeWhite, timeBlack, my_move) {
            if (!encodedPgn) {
                this.sendDataToLogins(
                    'game_finish',
                    {
                        game_id: gameId
                    },
                    []
                );
            }
            this.send(
                {
                    scope: 'send_to_game_observers',
                    method: 'send_pgn_to_observers',
                    data: {
                        game_id: gameId,
                        encoded_pgn: encodedPgn,
                        time_white: timeWhite,
                        time_black: timeBlack,
                        my_move: my_move
                    }
                }
            )
        }
    };
});