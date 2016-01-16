/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('RegisterCtrl', function ($scope, $rootScope, $http, $location, UserService) {
    $rootScope.user = {};
    $scope.errors = {};

    $scope.register = function() {
        UserService.register({
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