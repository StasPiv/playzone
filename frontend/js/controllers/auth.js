/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('AuthCtrl', function ($scope, $rootScope, $http, $location, UserRest, CookieService, WebsocketService) {
    $rootScope.user = new UserRest();
    $scope.errors = {};

    $scope.auth = function() {
        $rootScope.user.$auth().then(
            function() {
                $scope.errors = {};
                CookieService.rememberUser($rootScope.user);
                $location.path('/');
                WebsocketService.introduction($rootScope.user);
            },
            function(response) {
                $scope.errors = response.data;
            }
        );
    }
});