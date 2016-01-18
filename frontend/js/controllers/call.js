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

    $scope.call = new CallRest;

    $scope.sendCall = function(call, calls_from_me) {
        call.$send().then(
            function(response) {
                $scope.errors = {};
                angular.forEach(response.data, function(value, key) {
                    calls_from_me.push(new CallRest(value));
                });
            },
            function (response) {
                $scope.errors = response.data.errors;
            }
        );
    };
});