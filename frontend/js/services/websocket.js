/**
 * Created by stas on 24.01.16.
 */
'use strict';

playzoneServices.factory('WebsocketService', function($websocket) {
    var listenersMap = {};
    // Open a WebSocket connection
    var dataStream = $websocket('ws://localhost:1234/');

    dataStream.onMessage(
        function(message) {
            console.log(message, '4');
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
        sendGameToObservers: function(gameId, encodedPgn) {
            console.log('sendGameToObservers');
            this.send(
                {
                    scope: 'send_to_game_observers',
                    method: 'send_pgn_to_observers',
                    data: {
                        game_id: gameId,
                        encoded_pgn: encodedPgn
                    }
                }
            )
        }
    };
});