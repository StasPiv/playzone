/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, TimecontrolRest, CallRest, WebsocketService) {
    $scope.timecontrols = TimecontrolRest.query();

    $scope.colors = [
        {id: 'random', name: 'Random'},
        {id: 'w', name: 'White'},
        {id: 'b', name: 'Black'}
    ];

    $scope.sendCall = function(call) {
        CallRest.send(
            {},
            call,
            function(response) {
                var newCallIds = [];
                angular.forEach(response, function(call) {
                    $scope.calls_from_me.push(call);
                    newCallIds.push(call.id);
                });
                $scope.errors = {};

                WebsocketService.sendDataToLogins(
                    'call_send',
                    {
                        login: call.player,
                        call_ids: newCallIds
                    },
                    [call.player]
                );
                call.player = ""; // to prevent duplicate calls
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };
});