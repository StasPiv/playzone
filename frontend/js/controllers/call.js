/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('CallCtrl', function ($scope, TimeControlService) {
    TimeControlService.initTimeControls($scope);

    $scope.colors = [
        {id: '0', name: 'Random'},
        {id: '1', name: 'White'},
        {id: '2', name: 'Black'}
    ];
});