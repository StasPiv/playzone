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
]).run(['$http', '$rootScope', '$cookies', 'UserRest', 'WebsocketService', '$interval', function ($http, $rootScope, $cookies, UserRest, WebsocketService, $interval) {

    $rootScope.browserSupported = typeof(WebSocket) === "function";

    $rootScope.user = new UserRest({
        login: $rootScope.user ? $rootScope.user.login : $cookies.get("user_login"),
        token: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token"),
        password: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token")
    });

    $rootScope.user.$auth().then(
        function() {
            WebsocketService.introduction($rootScope.user);
            $interval(
                function () {
                    WebsocketService.reconnect($rootScope.user);
                },
                5000
            );
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