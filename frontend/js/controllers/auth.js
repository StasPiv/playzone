/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('AuthCtrl', function ($scope, $rootScope, $http, $location, UserService) {
    $rootScope.user = {};
    $scope.errors = {};

    $scope.auth = function() {
        UserService.auth({
            user: $scope.user,
            success: function(data) {
                $scope.errors = {};
                $location.path('/');
            },
            error: function(data) {
                $scope.errors = data.errors;
            }
        });
    }
});