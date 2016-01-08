/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneServices = angular.module('playzoneServices',[]);

playzoneServices.factory('EnvService', function() {
    return {
        testMode: 0,
        prodMode: 1,
        currentMode : 1
    };
});

playzoneServices.factory('ApiService', function(EnvService) {
    var API_URL,
        registerUrl,
        authUrl,
        getUserUrl;

    switch (EnvService.currentMode) {
        case EnvService.testMode:
            API_URL = 'http://playzone-test-api.lc';
            registerUrl = API_URL + '/?method=register';
            authUrl = API_URL + '/?method=auth';
            getUserUrl = API_URL + '?method=getuser';
            break;
        case EnvService.prodMode:
            API_URL = 'http://api.playzone-angular.lc/app_dev.php/';
            registerUrl = API_URL + 'user/register';
            authUrl = API_URL + 'user/auth';
            getUserUrl = API_URL + 'user';
    }

    return {
        register : registerUrl,
        auth : authUrl,
        getUser : getUserUrl
    };
});

playzoneServices.factory('UserService', function($http, $rootScope, $cookies, ApiService) {
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
                    $cookies.put('user_login', user.login);
                    $cookies.put('user_password', user.password);
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
                    $cookies.put('user_login', user.login);
                    $cookies.put('user_password', user.password);
                }
                onSuccess(data);
            })
            .error(function(data) {
                $rootScope.user.isAuth = false;
                onError(data);
            });
        },
        getUser : function(params) {
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'GET',
                url     : ApiService.getUser
            })
            .success(function(data) {
                onSuccess(data);
            })
            .error(function(data) {
                onError(data);
            });
        }
    };
});
