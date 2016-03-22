/**
 * Created by stas on 27.02.16.
 */
'use strict';

playzoneServices.factory('WebRTCService', function($websocket, $rootScope, $location, WebsocketService) {
    var wsSignalerPath = 'ws://ws.' + $location.host() + ':8081/signaler';
    var wsSignaler = $websocket(wsSignalerPath);

    var receiveChannel;
    var leaveRoomHandlers = {};
    var onMessageHandlers = {};
    var ownerConnection,
        subscriberConnection;

    var sendChannel;

    var ownerCandidate,
        offerSdpSescription,
        answerSdpSescription;

    function createNewOffer() {
        ownerConnection.createOffer().then(function (offer) {
            offerSdpSescription = offer.sdp;
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
                answerSdpSescription = answer.sdp;
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
            sdp: receivedMessage.answer_sdp_description
        }));
        console.log('owner addIceCandidate');
        ownerConnection.addIceCandidate(new RTCIceCandidate({
            sdpMLineIndex: 0,
            candidate: receivedMessage.candidate
        }), onAddIceCandidateSuccess, onAddIceCandidateError);
    }

    function setRemoteDescriptionAndIceCandidateToSubscriber(receivedMessage) {
        console.log('subscriber setRemoteDescription');
        subscriberConnection.setRemoteDescription(new RTCSessionDescription(
            {
                type: 'offer',
                sdp: receivedMessage.offer_sdp_description
            }
        ));

        createAnswer();

        console.log('subscriber addIceCandidate');
        subscriberConnection.addIceCandidate(new RTCIceCandidate({
            sdpMLineIndex: 0,
            candidate: receivedMessage.candidate
        }), onAddIceCandidateSuccess, onAddIceCandidateError);
    }

    wsSignaler.onMessage(function (webSocketMessage) {
        console.log(webSocketMessage, '1');
        var receivedMessage = angular.fromJson(webSocketMessage.data);

        switch (receivedMessage.action) {
            case 'offer-from-owner':
                createSubscriberConnectionAndChannel(receivedMessage.room);
                setRemoteDescriptionAndIceCandidateToSubscriber(receivedMessage);
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
        subscriberConnection = new RTCPeerConnection();

        subscriberConnection.onicecandidate = function (event) {
            if (!event.candidate) {
                return;
            }

            console.log('candidate', event.candidate.candidate);
            console.log('answer_sdp_description', answerSdpSescription);
            ownerCandidate = event.candidate;

            wsSignaler.send({
                action: 'subscriber-send-data',
                room: room,
                name: $rootScope.user.login,
                answer_sdp_description: answerSdpSescription,
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
                        console.log('need to move', '2');
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
        ownerConnection = new RTCPeerConnection();

        ownerConnection.onicecandidate = function (event) {
            if (!event.candidate) {
                return;
            }

            console.log('candidate', event.candidate.candidate);
            console.log('offer_sdp_description', offerSdpSescription);
            ownerCandidate = event.candidate;

            wsSignaler.send({
                action: 'owner-enter',
                room: room,
                offer_sdp_description: offerSdpSescription,
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
                        console.log('need to move', '2');
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
            return; // disable webRTC for test
            sendChannel && sendChannel.readyState === 'open' && sendChannel.send(messageJson);
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