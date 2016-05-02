/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, $rootScope, $location, CallRest, WebsocketService, AudioService, GameRest) {
    $scope.colors = [
        {id: 'random', name: 'Random'},
        {id: 'w', name: 'White'},
        {id: 'b', name: 'Black'}
    ];

    $scope.call = {
        time: {
            base_minutes: 3
        }
    };

    $scope.sendCall = function(call) {
        $('.footer .overlay').hide();

        if ($('#login_enemy').val() === "Robot") {
            $scope.playAgainstRobot(call);
            return;
        }

        CallRest.send(
            {},
            call,
            function(responseCall) {
                var newCallIds = [];
                $scope.calls_from_me && $scope.calls_from_me.push(responseCall);
                newCallIds.push(responseCall.id);
                $scope.errors = {};

                WebsocketService.sendDataToLogins(
                    'call_send',
                    {
                        login: call && call.player ? call.player : "",
                        call_ids: newCallIds
                    },
                    call && call.player ? [call.player] : []
                );
                call && (call.player = ""); // to prevent duplicate calls
                $location.path('/games');
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };

    $scope.playAgainstRobot = function(call) {
        $('.footer .overlay').hide();

        call.time.base = call.time.base_minutes * 60000;
        GameRest.createNewrobot(
            {
                time: call.time,
                color: !call.color ? ["w", "b"][parseInt(Math.random() * 2)] : call.color.id
            },
            function (newGame) {
                WebsocketService.sendDataToLogins(
                    'call_accept',
                    {
                        game_id: newGame.id
                    },
                    []
                );
                $scope.current.push(newGame);
                AudioService.newGame();
                $location.path( '/play/' + newGame.id );

            }
        );
    };
});