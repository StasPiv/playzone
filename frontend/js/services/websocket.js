/**
 * Created by stas on 24.01.16.
 */
'use strict';

playzoneServices.factory('WebsocketService', function($websocket, $location, $interval) {
    var listenersMap = {};
    // Open a WebSocket connection
    var webSocketPath = 'ws://ws.' + $location.host() + ':8081/';
    var dataStream;

    function createDataStream() {
        dataStream = $websocket(webSocketPath);
        dataStream.onMessage(
            function (message) {
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
    }

    createDataStream();

    return {
        reconnect: function (user) {
            if (dataStream.readyState !== 1) {
                createDataStream();
                this.introduction(user);
            }
        },
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
         * @param opponentTime
         * @param color
         */
        sendGameToObservers: function (gameId, encodedPgn, opponentTime, color) {
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
                        time: opponentTime,
                        color: color
                    }
                }
            )
        }
    };
});