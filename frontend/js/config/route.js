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
                    factory: checkIfAuthorized,
                    current: function (GameRest) {
                        return GameRest.query({status: "play", user:"all"}).$promise;
                    }
                }
            }).
            when('/play/:gameId', {
                templateUrl: 'partials/play.html?v=1',
                controller: 'PlayCtrl'
            }).
            when('/show/:gameId', {
                templateUrl: 'partials/show.html',
                controller: 'ShowCtrl'
            }).
            when('/players', {
                templateUrl: 'partials/online.html?v=22',
                controller: 'OnlineCtrl'
            }).
            when('/tournaments', {
                templateUrl: 'partials/tournaments.html',
                controller: 'TournamentsCtrl'
            }).
            when('/tournament/:tournamentId', {
                templateUrl: 'partials/tournament.html?v=23',
                controller: 'TournamentCtrl'
            }).
            when('/profile', {
                templateUrl: 'partials/myprofile.html?v=1',
                controller: 'MyProfileCtrl'
            }).
            when('/user/:userId/archive', {
                templateUrl: 'partials/user_archive.html?v=22',
                controller: 'UserArchiveCtrl'
            }).
            when('/user/:userId/profile', {
                templateUrl: 'partials/profile.html?v=22',
                controller: 'ProfileCtrl'
            }).
            when('/webrtc-share', {
                templateUrl: 'partials/webrtc-share.html',
                controller: 'WebrtcShareCtrl'
            }).
            when('/problems', {
                templateUrl: 'partials/problems.html',
                controller: 'ProblemsCtrl'
            }).
            otherwise({
                redirectTo: '/'
            });
    }]);