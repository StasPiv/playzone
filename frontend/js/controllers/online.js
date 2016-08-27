/**
 * Created by stas on 08.04.16.
 */
'use strict';

playzoneControllers.controller('OnlineCtrl', function ($scope, $rootScope, CallRest, UserRest, $interval) {
    $scope.openSendCallToPlayer = function (player) {
        $('#login_enemy').val(player.login).trigger('change');
    };

    $rootScope.fetchOnline();

    $scope.top = UserRest.query(
        {
            order_by: "u.rating",
            limit: 100,
            filter: {
                games_count: true,
                only_active: true
            }
        }
    );
});