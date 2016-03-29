/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, CallRest, WebsocketService) {
    $scope.colors = [
        {id: 'random', name: 'Random'},
        {id: 'w', name: 'White'},
        {id: 'b', name: 'Black'}
    ];

    $scope.sendCall = function(call) {
        call.time.base *= 1000 * 60; // time passed in ms
        CallRest.send(
            {},
            call,
            function(responseCall) {
                call.time.base /= 1000 * 60; // convert back to minutes
                var newCallIds = [];
                $scope.calls_from_me.push(responseCall);
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
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };
});