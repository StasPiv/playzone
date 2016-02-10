/**
 * Created by stas on 16.01.16.
 */
'use strict';

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
            when('/play/:gameId', {
                templateUrl: 'partials/play.html',
                controller: 'PlayCtrl'
            }).
            when('/show/:gameId', {
                templateUrl: 'partials/show.html',
                controller: 'ShowCtrl'
            }).
            otherwise({
                redirectTo: '/'
            });
    }]);