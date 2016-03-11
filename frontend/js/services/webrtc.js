/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function($websocket, $rootScope) {
    var wsSignaler = $websocket('ws://localhost:1234/signaler');

    function createNewOffer() {
        ownerConnection.createOffer().then(function (offer) {
            offerSdpDescription = offer.sdp;
            return ownerConnection.setLocalDescription(offer);
        }, function (error) {
            console.log('create offer error', error);
        })
            .then(function () {

            }, function (error) {
                console.log('second error', error);
            })
            .catch(function (error) {
                console.log('third error', error);
            });
    }

    function createAnswer() {
        subscriberConnection.createAnswer().then(
            function (answer) {
                answerSdpDescription = answer.sdp;
                return subscriberConnection.setLocalDescription(answer);
            }, function (error) {
                console.log('create answer error', error);
            }
        )
            .then(function () {

            }, function (error) {
                console.log('create answer promise second error', error);
            })
            .catch(function (error) {
                console.log('create answer promise third error', error);
            });
    }

    function setRemoteDescriptionAndIceCandidateToOwner(receivedMessage) {
        console.log('owner setRemoteDescription');
        ownerConnection.setRemoteDescription(new RTCSessionDescription({
            type: 'answer',
            sdp: receivedMessage.answerSdpDescription
        }));
        console.log('owner addIceCandidate');
        ownerConnection.addIceCandidate(new RTCIceCandidate({
            sdpMLineIndex: 0,
            candidate: receivedMessage.candidate
        }), onAddIceCandidateSuccess, onAddIceCandidateError);
    }

    wsSignaler.onMessage(function (webSocketMessage) {
        console.log(webSocketMessage);
        var receivedMessage = angular.fromJson(webSocketMessage.data);

        switch (receivedMessage.action) {
            case 'offer-from-owner':
                createSubscriberConnectionAndChannel(receivedMessage.room);

                console.log('subscriber setRemoteDescription');
                subscriberConnection.setRemoteDescription(new RTCSessionDescription(
                    {
                        type: 'offer',
                        sdp: receivedMessage.offerSdpDescription
                    }
                ));

                createAnswer();

                console.log('subscriber addIceCandidate');
                subscriberConnection.addIceCandidate(new RTCIceCandidate({
                    sdpMLineIndex: 0,
                    candidate: receivedMessage.candidate
                }), onAddIceCandidateSuccess, onAddIceCandidateError);

                break;
            case 'answer-from-subscriber':
                setRemoteDescriptionAndIceCandidateToOwner(receivedMessage);
                break;
            case 'subscriber-entered':
                createOwnerConnectionAndChannel(receivedMessage.room);
                createNewOffer();
                break;
        }
    });

    var receiveChannel;
    var leaveRoomHandlers = {};
    var onMessageHandlers = {};
    var ownerConnection,
        subscriberConnection;

    var sendChannel;

    var ownerCandidate,
        offerSdpDescription,
        answerSdpDescription;

    function getICEServers() {
        var isChrome = !!navigator.webkitGetUserMedia;
        var isFirefox = !!navigator.mozGetUserMedia;
        var chromeVersion = !!navigator.mozGetUserMedia ? 0 : parseInt(navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./)[2]);
        var iceServers = [];
        if (isFirefox) {
            iceServers.push({
                url: 'stun:23.21.150.121'
            });

            iceServers.push({
                url: 'stun:stun.services.mozilla.com'
            });
        }

        if (isChrome) {
            iceServers.push({
                url: 'stun:stun.l.google.com:19302'
            });

            iceServers.push({
                url: 'stun:stun.anyfirewall.com:3478'
            });
        }

        if (isChrome && chromeVersion < 28) {
            iceServers.push({
                url: 'turn:homeo@turn.bistri.com:80?transport=udp',
                credential: 'homeo'
            });

            iceServers.push({
                url: 'turn:homeo@turn.bistri.com:80?transport=tcp',
                credential: 'homeo'
            });
        }

        if (isChrome && chromeVersion >= 28) {
            iceServers.push({
                url: 'turn:turn.bistri.com:80?transport=udp',
                credential: 'homeo',
                username: 'homeo'
            });

            iceServers.push({
                url: 'turn:turn.bistri.com:80?transport=tcp',
                credential: 'homeo',
                username: 'homeo'
            });

            iceServers.push({
                url: 'turn:turn.anyfirewall.com:443?transport=tcp',
                credential: 'webrtc',
                username: 'webrtc'
            });
        }
    }

    function onAddIceCandidateSuccess() {
        trace('AddIceCandidate success.');
    }

    function onAddIceCandidateError(error) {
        trace('Failed to add Ice Candidate: ' + error.toString());
    }

    function onReceiveChannelStateChange() {
        var readyState = receiveChannel.readyState;
        trace('Receive channel state is: ' + readyState);
    }

    function createSubscriberConnectionAndChannel(room) {
        subscriberConnection = new RTCPeerConnection({
            iceServers: getICEServers()
        });

        subscriberConnection.onicecandidate = function (event) {
            if (!event.candidate) {
                return;
            }

            console.log('candidate', event.candidate.candidate);
            console.log('answerSdpDescription', answerSdpDescription);
            ownerCandidate = event.candidate;

            wsSignaler.send({
                action: 'subscriber-send-data',
                room: room,
                name: $rootScope.user.login,
                answerSdpDescription: answerSdpDescription,
                candidate: event.candidate.candidate
            });
        };

        subscriberConnection.ondatachannel = function (event) {
            trace('Receive Channel Callback');
            receiveChannel = event.channel;
            receiveChannel.onmessage = function (event) {
                trace('Received Message');
                console.log('Receive: ', event);
                angular.forEach(
                    onMessageHandlers,
                    function (callback) {
                        console.log('need to move');
                        callback(angular.fromJson(event.data));
                    }
                )
            };
            receiveChannel.onopen = onReceiveChannelStateChange;
            receiveChannel.onclose = onReceiveChannelStateChange;
        };

        console.log('subscriber create send channel');
        sendChannel = subscriberConnection.createDataChannel('Send data channel');
    }

    function createOwnerConnectionAndChannel(room) {
        ownerConnection = new RTCPeerConnection({
            iceServers: getICEServers()
        });

        ownerConnection.onicecandidate = function (event) {
            if (!event.candidate) {
                return;
            }

            console.log('candidate', event.candidate.candidate);
            console.log('offerSdpDescription', offerSdpDescription);
            ownerCandidate = event.candidate;

            wsSignaler.send({
                action: 'owner-enter',
                room: room,
                offerSdpDescription: offerSdpDescription,
                candidate: event.candidate.candidate
            });
        };

        ownerConnection.ondatachannel = function (event) {
            trace('Receive Channel Callback');
            receiveChannel = event.channel;
            receiveChannel.onmessage = function (event) {
                trace('Received Message');
                trace(event.data);
                console.log('Receive: ', event);
                angular.forEach(
                    onMessageHandlers,
                    function (callback) {
                        console.log('need to move');
                        callback(angular.fromJson(event.data));
                    }
                )
            };
            receiveChannel.onopen = onReceiveChannelStateChange;
            receiveChannel.onclose = onReceiveChannelStateChange;
        };

        sendChannel = ownerConnection.createDataChannel('Send data channel');
    }

    return {
        createRoom: function (room) {
            createOwnerConnectionAndChannel(room);
            createNewOffer();
        },
        joinRoom: function (room) {
            //createSubscriberConnectionAndChannel(room);
            wsSignaler.send({
                room: room,
                action: 'subscriber-enter',
                name: $rootScope.user.login
            });
        },
        leaveRoomHandler: function (userId) {
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
            console.log(sendChannel);
            sendChannel.send(JSON.stringify(message));
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