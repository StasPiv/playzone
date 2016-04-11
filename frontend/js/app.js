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
    $rootScope.connected = true;

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
                    WebsocketService.ping();
                },
                5000
            );
        }
    );

    $rootScope.loginsOnline = [];

    WebsocketService.addListener('listen_welcome', 'welcome', function (data) {
        $rootScope.loginsOnline = data['other_logins'];
        $rootScope.connected = true;
    });

    WebsocketService.addListener('listen_user_in', 'user_in', function (user) {
        $rootScope.loginsOnline.indexOf(user['login']) === -1 && $rootScope.loginsOnline.push(user['login']);
    });

    WebsocketService.addListener('listen_user_gone', 'user_gone', function (user) {
        var index = $rootScope.loginsOnline.indexOf(user['login']);
        if (index > -1) {
            $rootScope.loginsOnline.splice(index, 1);
        }
    });
}]);

var checkIfUnauthorized = function ($q, $rootScope, $location) {
    if ($rootScope.user && $rootScope.user.isAuth) {
        $location.path('/');
    }
};

var playzoneControllers = angular.module('playzoneControllers', []);

var playzoneServices = angular.module('playzoneServices', []);