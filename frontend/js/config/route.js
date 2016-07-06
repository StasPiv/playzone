/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneApp.config(['$routeProvider', '$locationProvider',
    function($routeProvider, $locationProvider) {
        $locationProvider.hashPrefix('');
        $routeProvider.
            when('/', {
                templateUrl: 'partials/home.html?v=1.0.1',
                controller: 'HomeCtrl',
                resolve: {
                    factory: checkIfNotMobile
                }
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
                templateUrl: 'partials/games.html?v=1.0.1',
                controller: 'GamesCtrl',
                resolve: {
                    factory: checkIfAuthorized
                }
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
                templateUrl: 'partials/online.html?v=0.0.1',
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
                templateUrl: 'partials/myprofile.html',
                controller: 'MyProfileCtrl'
            }).
            when('/user/:userId/archive', {
                templateUrl: 'partials/user_archive.html',
                controller: 'UserArchiveCtrl'
            }).
            when('/user/:userId/profile', {
                templateUrl: 'partials/profile.html',
                controller: 'ProfileCtrl'
            }).
            otherwise({
                redirectTo: '/'
            });
    }]);