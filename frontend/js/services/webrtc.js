/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function() {
    var channel = new DataChannel();
    var signaler = initReliableSignaler(channel, ':8080/');

    return {
        createRoom: function (roomid) {
            signaler.createNewRoomOnServer(roomid, function () {
                channel.userid = roomid;
                channel.transmitRoomOnce = true;
                channel.open(roomid);
                console.log('channel is open',roomid);
            });
        },
        joinRoom: function (roomid) {
            signaler.getRoomFromServer(roomid, function (roomid) {
                channel.connect(roomid); // setting 'channel' & connecting socket

                // setting 'roomToken' and 'broadcaster' and joining
                channel.join({
                    id: roomid,
                    owner: roomid
                });
                console.log('channel is connected',roomid);
            });
        },
        addMessageListener: function (callback) {
            if (typeof callback === 'function') {
                channel.onmessage = callback;
            }
        },
        sendMessage: function (message) {
            channel.send(message);
        },
        getGameRoomName: function (gameId) {
            return 'game' + gameId;
        },
        createGameRoom: function (gameId) {
            this.createRoom(this.getGameRoomName(gameId));
        },
        joinGameRoom: function (gameId) {
            this.joinRoom(this.getGameRoomName(gameId));
        }
    };
});