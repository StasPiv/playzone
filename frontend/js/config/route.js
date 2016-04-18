/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneApp.config(['$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        $locationProvider.hashPrefix('');
        $routeProvider.
            when('/', {
                templateUrl: 'partials/games.html',
                controller: 'GamesCtrl'
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
            when('/online', {
                templateUrl: 'partials/online.html',
                controller: 'OnlineCtrl'
            }).
            when('/tournaments', {
                templateUrl: 'partials/tournaments.html',
                controller: 'TournamentsCtrl'
            }).
            when('/tournament/:tournamentId', {
                templateUrl: 'partials/tournament.html',
                controller: 'TournamentCtrl'
            }).
            when('/profile', {
                templateUrl: 'partials/profile.html',
                controller: 'ProfileCtrl'
            }).
            otherwise({
                redirectTo: '/'
            });
    }]);