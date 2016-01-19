/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('RegisterCtrl', function ($scope, $rootScope, $http, $location, CookieService, UserRest) {

    $rootScope.user = {};
    $scope.errors = {};

    $scope.register = function() {
        $rootScope.user = new UserRest($scope.user);
        $rootScope.user.$register().then(
            function() {
                $scope.errors = {};
                CookieService.rememberUser($rootScope.user);
                $location.path('/');
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    }
});