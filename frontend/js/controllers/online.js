/**
 * Created by stas on 08.04.16.
 */
'use strict';

playzoneControllers.controller('OnlineCtrl', function ($scope, $rootScope, CallRest, UserRest) {
    $scope.openSendCallToPlayer = function (player) {
        $('#login_enemy').val(player.login).trigger('change');
    };

    $scope.top_5 = UserRest.query({order_by: "u.win", limit: 5});
});