/**
 * Created by stas on 08.04.16.
 */
'use strict';

playzoneControllers.controller('OnlineCtrl', function ($scope, $rootScope, CallRest) {
    console.log($rootScope.loginsOnline);
    
    $scope.openSendCallToPlayer = function (player) {
        $('#login_enemy').val(player).trigger('change');
    };
});