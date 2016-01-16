/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, TimeControlService, CallService, GameService) {
    TimeControlService.initTimeControls($scope);

    $scope.colors = [
        {id: 'random', name: 'Random'},
        {id: 'white', name: 'White'},
        {id: 'black', name: 'Black'}
    ];

    $scope.sendCall = function() {
        CallService.sendCall({
            call: $scope.call,
            success: function() {
                $scope.errors = {};
                GameService.initCallsFromMe($scope);
            },
            error: function(data) {
                $scope.errors = data.errors;
            }
        });
    }
});