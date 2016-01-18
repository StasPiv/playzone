/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneApp = angular.module('playzoneApp', [
    'ngRoute',
    'ngCookies',
    'ngResource',
    'playzoneControllers',
    'playzoneServices',
    'pascalprecht.translate'
]).run(['$http', '$rootScope', '$cookies', 'UserRest', function($http, $rootScope, $cookies, UserRest) {

    $rootScope.user = new UserRest({
        login: $cookies.get("user_login"),
        token: $cookies.get("user_token"),
        password: $cookies.get("user_token")
    });

    $rootScope.user.$auth();

}]);

var checkIfUnauthorized = function ($q, $rootScope, $location) {
    if ($rootScope.user && $rootScope.user.isAuth) {
        $location.path('/');
    }
};

var playzoneControllers = angular.module('playzoneControllers', []);

var playzoneServices = angular.module('playzoneServices',[]);