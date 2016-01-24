/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, TimecontrolRest, CallRest, WebsocketService) {
    $scope.timecontrols = TimecontrolRest.query();

    $scope.colors = [
        {id: 'random', name: 'Random'},
        {id: 'white', name: 'White'},
        {id: 'black', name: 'Black'}
    ];

    $scope.sendCall = function(call) {
        CallRest.send(
            {},
            call,
            function(response) {
                angular.forEach(response, function(value) {
                    $scope.calls_from_me.push(value);
                });
                $scope.errors = {};
                WebsocketService.sendDataToLogins('call_send', response, [call.player]);
                call.player = ""; // to prevent duplicate calls
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };
});