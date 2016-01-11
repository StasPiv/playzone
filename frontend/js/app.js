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
}]).config(['$translateProvider',
        function ($translateProvider) {
            // configures staticFilesLoader
            $translateProvider.useStaticFilesLoader({
                prefix: 'translations/',
                suffix: '.json'
            });
            // load 'en' table on startup
            $translateProvider.preferredLanguage('en');
            // remember language
            $translateProvider.useLocalStorage();
        }]
);

playzoneApp.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'partials/home.html',
                controller: 'HomeCtrl'
            }).
            when('/register', {
                templateUrl: 'partials/register.html',
                controller: 'RegisterCtrl',
                resolve: {
                    factory: checkIfUnauthorized
                }
            }).
            when('/auth', {
                templateUrl: 'partials/auth.html',
                controller: 'AuthCtrl',
                resolve: {
                    factory: checkIfUnauthorized
                }
            }).
            when('/games', {
                templateUrl: 'partials/games.html',
                controller: 'GamesCtrl'
            }).
            otherwise({
                redirectTo: '/'
            });
    }]);

var checkIfUnauthorized = function ($q, $rootScope, $location) {
    if ($rootScope.user && $rootScope.user.isAuth) {
        $location.path('/');
    }
};
