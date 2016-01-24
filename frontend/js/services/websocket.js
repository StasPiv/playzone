/**
 * Created by stas on 24.01.16.
 */
'use strict';

playzoneServices.factory('WebsocketService', function($websocket) {
    var listeners = [];
    // Open a WebSocket connection
    var dataStream = $websocket('ws://localhost:1234/');

    dataStream.onMessage(
        function(message) {
            console.log(message);
        }
    );

    return {
        addListener: function(listenerName, methodToListen, callback) {
            if (listeners.indexOf(listenerName) !== -1) {
                return;
            }
            if (typeof callback !== 'function') {
                return;
            }
            dataStream.onMessage(
                function (message) {
                    var receivedMessage = angular.fromJson(message.data);

                    if (receivedMessage.method !== methodToListen) {
                        return;
                    }

                    callback(receivedMessage.data);
                }
            );
            listeners.push(listenerName);
        },
        send: function(data) {
            var dataToSend = angular.toJson(data);
            console.log(data);
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
        }
    };
});