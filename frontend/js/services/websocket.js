/**
 * Created by stas on 24.01.16.
 */
'use strict';

playzoneServices.factory('WebsocketService', function($websocket, $location, $rootScope, $timeout, $interval, UserRest) {
    var listenersMap = {};
    // Open a WebSocket connection
    var webSocketPath = 'ws://ws.' + $location.host() + ':8081/';
    var webSocketEchoPath = 'ws://ws.' + $location.host() + ':8081/echo3';

    var dataStream;

    function createDataStream() {
        dataStream = $websocket(webSocketPath);
        dataStream.onMessage(
            function (message) {

                console.log("message", message);
                
                var receivedMessage = angular.fromJson(message.data);
                console.log("received message", receivedMessage);

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

    var echoStream = $websocket(webSocketEchoPath);
    var counter = 0;
    var stop = false;

    echoStream.onMessage(
        function () {
            if (stop) {
                return;
            }
            echoStream.send("hello");
            counter++;
        }
    );

    return {
        checkLag: function (callback) {
            stop = false;
            echoStream.send("hello");
            counter = 0;

            $timeout(
                function () {
                    stop = true;
                    var lag = 3  / counter;
                    UserRest.ping(
                        {
                            lag: lag
                        },
                        typeof callback == "function" ? callback(lag) : null
                    );
                },
                3000
            );
        },

        ping: function () {
            $rootScope.connected = dataStream.readyState === 1;
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
        removeListeners: function (listeners, exceptions) {
            angular.forEach(listenersMap, function (value, key) {
                if (listeners) { // for particular listeners
                    if (listeners.indexOf(key) !== -1) { // if we found in listeners
                        delete listenersMap[key];
                    }
                    return;
                }

                if (!exceptions || exceptions.indexOf(key) === 1) { // otherwise check exceptions
                    delete listenersMap[key];
                }
            });
        },
        send: function(data) {
            console.log(data);
            var date = new Date();
            data.data.milliseconds = date.getTime();
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
         * @param color
         */
        sendGameToObservers: function (gameId, encodedPgn, timeWhite, timeBlack, color) {
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
                        color: color
                    }
                }
            )
        },

        /**
         * @param gameId
         * @param move
         * @param timeWhite
         * @param timeBlack
         * @param color
         * @param moveNumber
         * @param fen
         */
        sendMoveToObservers: function (gameId, move, timeWhite, timeBlack, color, moveNumber, fen) {
            this.send(
                {
                    scope: 'send_to_game_observers',
                    method: 'send_pgn_to_observers',
                    data: {
                        game_id: gameId,
                        move: move,
                        move_number: moveNumber,
                        time_white: timeWhite,
                        time_black: timeBlack,
                        color: color,
                        fen: fen
                    }
                }
            )
        },
        
        /**
         * @param chatRoom
         * @param message
         */
        sendMessageToObservers: function (chatRoom, message) {
            this.send(
                {
                    scope: 'send_to_users',
                    method: 'send_message_to_observers_' + chatRoom,
                    data: {
                        game_id: chatRoom,
                        message: message
                    }
                }
            )
        }
    };
});