/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function($websocket, $rootScope) {
    var wsSignaler = $websocket('ws://localhost:1234/signaler');
    var ownerConnection,
        subscriberConnection,
        receiveChannel;
    var leaveRoomHandlers = {};
    var onMessageHandlers = {};

    var isChrome = !!navigator.webkitGetUserMedia;
    var isFirefox = !!navigator.mozGetUserMedia;
    var chromeVersion = !!navigator.mozGetUserMedia ? 0 : parseInt(navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./)[2]);

    var iceServers = [];

    var channel;

    var preparedOfferSdpDescription;
    var preparedSubscriberCandidate;
    var preparedOwnerCandidate;

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

    wsSignaler.onMessage(
        function (webSocketMessage) {
            var receivedMessage = angular.fromJson(webSocketMessage.data);
            console.log(receivedMessage);
            var room = receivedMessage.room;
            // TODO: implement open, connect and join to datachannel
            switch (receivedMessage.action) {
                case 'created':
                    console.log('I open a channel',room);
                    break;
                case 'subscriber-joined':
                    //wsSignaler.send({
                    //    room: room,
                    //    action: 'create',
                    //    name: $rootScope.user.login,
                    //    offer: preparedOfferSdpDescription
                    //});
                    //wsSignaler.send({
                    //    action: 'ice-candidate-from-owner',
                    //    room: room,
                    //    name: $rootScope.user.login,
                    //    candidate: {
                    //        sdpMLineIndex: preparedOwnerCandidate.sdpMLineIndex,
                    //        candidate: preparedOwnerCandidate.candidate
                    //    }
                    //});
                    break;
                case 'offer-from-owner':
                    console.log('Owner sent offer',room);
                    window.subscriberConnection = subscriberConnection =
                        new RTCPeerConnection({
                            iceServers: iceServers
                        });

                    if (preparedSubscriberCandidate) {
                        wsSignaler.send({
                            action: 'ice-candidate-from-subscriber',
                            room: room,
                            name: $rootScope.user.login,
                            candidate: {
                                sdpMLineIndex: preparedSubscriberCandidate.sdpMLineIndex,
                                candidate: preparedSubscriberCandidate.candidate
                            }
                        });
                        break;
                    }

                    channel = subscriberConnection.createDataChannel('sendDataChannel');

                    channel.onmessage = function (message) {
                        console.log('Receive: ' + message);
                        angular.forEach(
                            onMessageHandlers,
                            function (callback) {
                                callback(message);
                            }
                        )
                    };

                    subscriberConnection.onicecandidate = function (event) {
                        trace('subscriber ice callback');
                        if (event.candidate) {
                            preparedSubscriberCandidate = event.candidate;
                            wsSignaler.send({
                                action: 'ice-candidate-from-subscriber',
                                room: room,
                                name: $rootScope.user.login,
                                candidate: {
                                    sdpMLineIndex: event.candidate.sdpMLineIndex,
                                    candidate: event.candidate.candidate
                                }
                            });
                            trace('Remote ICE candidate: \n ' + event.candidate.candidate);
                        }
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

                    console.log('offerSDP', receivedMessage.offerSDP);
                    subscriberConnection.setRemoteDescription(
                        new RTCSessionDescription(
                            {
                                type: 'offer',
                                sdp: receivedMessage.offerSDP
                            }
                        )
                    );
                    channel = subscriberConnection.createDataChannel('sendDataChannel');
                    trace('Created send data channel');
                    subscriberConnection.createAnswer(
                        function (answerSdpDescription) {
                            subscriberConnection.setLocalDescription(answerSdpDescription);
                            trace('Answer from remoteConnection \n' + answerSdpDescription.sdp);
                            wsSignaler.send({
                                room: room,
                                action: 'join-and-prepare-answer',
                                name: $rootScope.user.login,
                                answer: answerSdpDescription
                            });
                        },
                        function (error) {
                            trace('Failed to create session description: ' + error.toString());
                        }
                    );
                    break;
                case 'answer-from-subscriber':
                    console.log('Subscriber sent answer',room);
                    ownerConnection.setRemoteDescription(
                        new RTCSessionDescription(
                            {
                                type: 'answer',
                                sdp: receivedMessage.answerSDP
                            }
                        )
                    );
                    break;
                case 'subscriber-sent-ice-candidate':
                    console.log('Subscriber sent ice candidate',room);
                    console.log('receivedMessage.candidate', receivedMessage.candidate);
                    ownerConnection.addIceCandidate(
                        new RTCIceCandidate({
                            sdpMLineIndex: receivedMessage.candidate.sdpMLineIndex,
                            candidate: receivedMessage.candidate.candidate
                        }),
                        onAddIceCandidateSuccess, onAddIceCandidateError
                    );
                    break;
                case 'owner-sent-ice-candidate':
                    console.log('Owner sent ice candidate',room);
                    console.log('receivedMessage.candidate', receivedMessage.candidate);
                    subscriberConnection.addIceCandidate(
                        new RTCIceCandidate({
                            sdpMLineIndex: receivedMessage.candidate.sdpMLineIndex,
                            candidate: receivedMessage.candidate.candidate
                        }),
                        onAddIceCandidateSuccess, onAddIceCandidateError
                    );
                    break;
            }
        }
    );

    return {
        createRoom: function (room) {
            window.ownerConnection = ownerConnection =
                new RTCPeerConnection({
                    iceServers: iceServers
                });
            trace('Created local peer connection object localConnection');

            ownerConnection.onicecandidate = function (event) {
                trace('owner ice callback');
                if (event.candidate) {
                    preparedOwnerCandidate = event.candidate;
                    wsSignaler.send({
                        action: 'ice-candidate-from-owner',
                        room: room,
                        name: $rootScope.user.login,
                        candidate: {
                            sdpMLineIndex: event.candidate.sdpMLineIndex,
                            candidate: event.candidate.candidate
                        }
                    });

                    trace('Owner ICE candidate: \n' + event.candidate.candidate);
                }
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

            function onSendChannelStateChange() {
                var readyState = channel.readyState;
                trace('Send channel state is: ' + readyState);
            }

            channel = ownerConnection.createDataChannel('sendDataChannel');
            trace('Created send data channel');

            channel.onopen = onSendChannelStateChange;
            channel.onclose = onSendChannelStateChange;

            trace('Created remote peer connection object remoteConnection');

            console.log(ownerConnection);
            ownerConnection.createOffer(
                function (offerSdpDescription) {
                    trace('create offer callback');
                    ownerConnection.setLocalDescription(offerSdpDescription);
                    wsSignaler.send({
                        room: room,
                        action: 'create',
                        name: $rootScope.user.login,
                        offer: offerSdpDescription
                    });
                    preparedOfferSdpDescription = offerSdpDescription;
                    trace('Offer from localConnection \n' + offerSdpDescription.sdp);
                },
                function (error) {
                    trace('Failed to create session description: ' + error.toString());
                }
            );
        },
        joinRoom: function (roomid) {
            wsSignaler.send({
                room: roomid,
                action: 'join',
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
            console.log(message);
            var sendChannel = channel || receiveChannel;
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