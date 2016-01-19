/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, TimecontrolRest, CallRest) {
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
                call.player = ""; // to prevent duplicate calls
                $scope.errors = {};
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    };
});