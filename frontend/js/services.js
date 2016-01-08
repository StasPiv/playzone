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
        authUrl;

    switch (EnvService.currentMode) {
        case EnvService.testMode:
            API_URL = 'http://playzone-test-api.lc';
            registerUrl = API_URL + '/?method=register';
            authUrl = API_URL + '/?method=auth';
            break;
        case EnvService.prodMode:
            API_URL = 'http://api.playzone-angular.lc/app_dev.php/';
            registerUrl = API_URL + 'user/register';
            authUrl = API_URL + 'user/auth';
    }

    return {
        register : registerUrl,
        auth : authUrl
    };
});

playzoneServices.factory('UserService', function($http, $rootScope, ApiService) {
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
                }
                onSuccess(data);
            })
            .error(function(data) {
                $rootScope.user.isAuth = false;
                onError(data);
            });
        }
    };
});
