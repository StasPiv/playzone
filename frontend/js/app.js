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
]).run(['$http', '$rootScope', '$cookies', 'UserRest', 'ChatRest', 'WebsocketService', 'EnvService', '$interval', '$location', '$templateCache', function ($http, $rootScope, $cookies, UserRest, ChatRest, WebsocketService, EnvService, $interval, $location, $templateCache) {

    $rootScope.$on('$viewContentLoaded', function() {
        $templateCache.removeAll();
    });

    if ($location.host().indexOf('.lc') === -1) {
        console.log = function () {

        };
    }

    $rootScope.browserSupported = typeof(WebSocket) === "function";
    $rootScope.isMobile = EnvService.isMobile();
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
        var otherLogins = data['other_logins'];

        $.each(
            otherLogins,
            function (index, otherUser) {
                var existingUser = $rootScope.loginsOnline.searchById(otherUser['id']);

                if (!existingUser) {
                    $rootScope.loginsOnline.push(otherUser)
                } else if (!existingUser.count) {
                    existingUser.count = 1;
                } else {
                    existingUser.count++;
                }
            }
        );

        $rootScope.connected = true;
    });

    WebsocketService.addListener('listen_user_in', 'user_in', function (newUser) {
        var existingUser = $rootScope.loginsOnline.searchById(newUser['id']);

        if (!existingUser) {
            $rootScope.loginsOnline.push(newUser)
        } else if (!existingUser.count) {
            existingUser.count = 1;
        } else {
            existingUser.count++;
        }
    });

    WebsocketService.addListener('listen_user_gone', 'user_gone', function (user) {
        user = $rootScope.loginsOnline.searchById(user['id']);

        if (user) {
            user.count ? user.count-- : $rootScope.loginsOnline.pullById(user['id'])
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