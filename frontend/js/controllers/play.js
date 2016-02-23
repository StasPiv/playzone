/**
 * Created by stas on 30.01.16.
 */
'use strict';

playzoneControllers.controller('PlayCtrl', function ($scope, $rootScope, $routeParams, GameRest) {
    $scope.boardConfig = {
        pieceType: 'leipzig'
    };

    $scope.game = GameRest.get(
        {
            id: $routeParams.gameId
        }
    );

    // =====DATA CHANNEL=====

    // initializing DataChannel.js constructor.
    $scope.channel = new DataChannel();

    // using reliable-signaler
    var signaler = initReliableSignaler($scope.channel, ':8080/'),
        roomid = 'game' + $routeParams.gameId;

    if ($rootScope.user.login === 'TestLogin') {
        signaler.createNewRoomOnServer(roomid, function () {
            $scope.channel.userid = roomid;
            $scope.channel.transmitRoomOnce = true;
            $scope.channel.open(roomid);
        });
    } else {
        signaler.getRoomFromServer(roomid, function (roomid) {
            $scope.channel.connect(roomid); // setting 'channel' & connecting socket

            // setting 'roomToken' and 'broadcaster' and joining
            $scope.channel.join({
                id: roomid,
                owner: roomid
            });
        });
    }

});