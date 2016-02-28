/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneApp = angular.module('playzoneApp', [
    'ngRoute',
    'ngCookies',
    'ngResource',
    'ngWebSocket',
    'playzoneControllers',
    'playzoneServices',
    'pascalprecht.translate',
    'LocalStorageModule'
]).run(['$http', '$rootScope', '$cookies', 'UserRest', 'WebsocketService', function ($http, $rootScope, $cookies, UserRest, WebsocketService) {

    $rootScope.user = new UserRest({
        login: $cookies.get("user_login"),
        token: $cookies.get("user_token"),
        password: $cookies.get("user_token")
    });

    $rootScope.user.$auth().then(
        function() {
            WebsocketService.introduction($rootScope.user);
        }
    );
}]);

var checkIfUnauthorized = function ($q, $rootScope, $location) {
    if ($rootScope.user && $rootScope.user.isAuth) {
        $location.path('/');
    }
};

var playzoneControllers = angular.module('playzoneControllers', []);

var playzoneServices = angular.module('playzoneServices', []);