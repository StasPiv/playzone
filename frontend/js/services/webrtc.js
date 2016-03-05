/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function() {
    var channel = new DataChannel();
    var signaler = initReliableSignaler(channel, ':8080/');

    var roomMap = {};

    return {
        createRoom: function (roomid) {
            signaler.createNewRoomOnServer(roomid, function () {
                channel.userid = roomid;
                channel.transmitRoomOnce = true;
                channel.open(roomid);
                console.log('I open a channel',roomid);
            });
        },
        joinRoom: function (roomid) {
            var webRTC = this;
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
                channel.onleave = webRTC.ownerLeaveGame.bind(webRTC);
            });
        },
        ownerLeaveGame: function (ownerId) {
            if (ownerId.indexOf(this.getPrefixGameRoomName()) !== 0) {
                return;
            }
            console.log("owner left game");
            if (roomMap[ownerId] && roomMap[ownerId].owner === false) {
                delete roomMap[ownerId]; // remove room if owner was left
            }
        },
        addMessageListener: function (callback) {
            if (typeof callback === 'function') {
                channel.onmessage = callback;
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
        joinOrCreateGameRoom: function (gameId) {
            var gameRoomId = this.getGameRoomName(gameId);
            var webRTC = this;
            this.joinRoom(gameRoomId);
            setTimeout(function () { // create game room if it does not exist
                if (!roomMap[gameRoomId]) {
                    webRTC.createRoom(gameRoomId);
                    roomMap[gameRoomId] = {owner: true};
                }
            }, 500);
            setInterval(function () { // try to reconnect if owner was left
                if (!!roomMap[gameRoomId]) {
                    return;
                }
                webRTC.joinRoom(gameRoomId);
            }, 500);
        }
    };
});