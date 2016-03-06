/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function() {
    var channel = new DataChannel();
    var signaler = initReliableSignaler(channel, ':8080/');
    var roomMap = {};
    var leaveRoomHandlers = {};
    var onMessageHandlers = {};

    channel.onmessage = function (message) {
        angular.forEach(onMessageHandlers, function (callback) {
            callback(message);
        });
    };

    return {
        createRoom: function (roomid) {
            signaler.createNewRoomOnServer(roomid, function () {
                channel.userid = roomid;
                channel.transmitRoomOnce = true;
                channel.open(roomid);
                console.log('I open a channel',roomid);
                roomMap[roomid] = {owner: true};
            });
        },
        joinRoom: function (roomid) {
            channel.onleave = this.leaveRoomHandler.bind(this);
            signaler.getRoomFromServer(roomid, function (roomid) {
                if (!!roomMap[roomid]) {
                    return;
                }
                channel.connect(roomid); // setting 'channel' & connecting socket

                // setting 'roomToken' and 'broadcaster' and joining
                channel.join({
                    id: roomid,
                    owner: roomid
                });
                console.log('I join to a channel',roomid);
                roomMap[roomid] = {owner: false};
            });
        },
        isOwnerOfGameRoom: function (userId) {
            return userId.indexOf(this.getPrefixGameRoomName()) === 0;
        },
        leaveRoomHandler: function (userId) {
            if (this.isOwnerOfGameRoom(userId)) {
                console.log("owner left game");
                if (roomMap[userId] && roomMap[userId].owner === false) {
                    delete roomMap[userId]; // remove room if owner was left
                    this.joinRoom(userId); // wait until he is back (recursion on server side)
                }
            }
            angular.forEach(leaveRoomHandlers, function (callback) {
                callback();
            });
        },
        addMessageListener: function (callback, type) {
            if (typeof callback === 'function') {
                onMessageHandlers[type] = callback;
            }
        },
        sendMessage: function (message) {
            channel.send(message);
        },
        getPrefixGameRoomName: function () {
            return 'pfgame';
        },
        getGameRoomName: function (gameId) {
            return this.getPrefixGameRoomName() + gameId;
        },
        createGameRoom: function (gameId) {
            this.createRoom(this.getGameRoomName(gameId));
        },
        joinGameRoom: function (gameId) {
            this.joinRoom(this.getGameRoomName(gameId));
        },
        addCallBackLeaveRoom: function (id, callback) {
            leaveRoomHandlers[id] = callback;
        }
    };
});