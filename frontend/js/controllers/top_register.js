/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('TopRegisterCtrl', function ($scope, $rootScope, $cookies) {
    $scope.logout = function() {
        $cookies.remove("user_login");
        $cookies.remove("user_password");
        $rootScope.user = {};
    }
});