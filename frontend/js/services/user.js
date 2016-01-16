/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('UserService', function($http, $rootScope, $cookies, ApiService, CookieService) {
    return {
        register : function(params) {
            var user = params.user;
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'POST',
                url     : ApiService.register,
                data    : user, //forms user object
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data) {
                    if (data.data) {
                        $rootScope.user = data.data;
                        $rootScope.user.isAuth = true;
                        CookieService.rememberUser(user);
                    }
                    onSuccess(data);
                })
                .error(function(data) {
                    $rootScope.user.isAuth = false;
                    onError(data);
                });
        },
        auth : function(params) {
            var user = params.user;
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'POST',
                url     : ApiService.auth,
                data    : user, //forms user object
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data) {
                    if (data.data) {
                        $rootScope.user = data.data;
                        $rootScope.user.isAuth = true;
                        CookieService.rememberUser(user);
                    }
                    onSuccess(data);
                })
                .error(function(data) {
                    $rootScope.user.isAuth = false;
                    onError(data);
                });
        },
        initUsers : function(params, $scope) {
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'GET',
                url     : ApiService.get_users
            })
                .success(function(data) {
                    $scope.users = data.data;
                    $scope.usersHashMap = [];

                    angular.forEach($scope.users, function(value, key) {
                        $scope.usersHashMap[value.id] = value.login;
                    });

                    onSuccess(data);
                })
                .error(function(data) {
                    onError(data);
                });
        }
    };
});