/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneApp = angular.module('playzoneApp', [
    'ngRoute',
    'ngCookies',
    'playzoneControllers',
    'playzoneServices',
    'pascalprecht.translate'
]).run(['$http', '$rootScope', '$cookies', 'UserService', 'TimeControlService', function($http, $rootScope, $cookies, UserService, TimeControlService) {
    $rootScope.user = {};

    if ($cookies.get("user_login") && $cookies.get("user_password")) {
        UserService.auth({
            user: {
                "login" : $cookies.get("user_login"),
                "password" : $cookies.get("user_password")
            },
            success: function() {

            },
            error: function() {

            }
        });
    }
}]);

var checkIfUnauthorized = function ($q, $rootScope, $location) {
    if ($rootScope.user && $rootScope.user.isAuth) {
        $location.path('/');
    }
};

var playzoneControllers = angular.module('playzoneControllers', []);

var playzoneServices = angular.module('playzoneServices',[]);