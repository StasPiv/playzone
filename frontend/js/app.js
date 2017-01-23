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
]).run(['$http', '$rootScope', '$cookies', 'UserRest', 'ChatRest', 'WebsocketService', 'EnvService', '$interval', '$location', '$templateCache', 'TournamentRest', 'AudioService', function ($http, $rootScope, $cookies, UserRest, ChatRest, WebsocketService, EnvService, $interval, $location, $templateCache, TournamentRest, AudioService) {

    $rootScope.location = $location;

    $rootScope.$on('$viewContentLoaded', function() {
        $templateCache.removeAll();
    });

    if ($location.host().indexOf('.lc') === -1) {
        console.log = function () {

        };
    }

    if ($location.host() === 'playzone.immortalchess.net') {
        window.location.href = window.location.href.replace($location.host(), 'pozitiffchess.net');
    }

    console.log($location.host());

    $rootScope.browserSupported = typeof(WebSocket) === "function";
    $rootScope.isMobile = EnvService.isMobile();
    $rootScope.connected = true;

    $rootScope.user = UserRest.auth(
        "",
        {
            login: $rootScope.user ? $rootScope.user.login : $cookies.get("user_login"),
            token: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token"),
            password: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token")
        },
        function(user) {
            $rootScope.authError = false;
            WebsocketService.introduction(user);
            $interval(
                function () {
                    WebsocketService.ping();
                    $rootScope.user.$profile().then(
                        function () {
                            $rootScope.user.login = $cookies.get("user_login");
                            $rootScope.user.token = $cookies.get("user_token");
                        }
                    );
                },
                5000
            );
        },
        function (errorData) {
            if (errorData.status === 403 && errorData.data.login) {
                $rootScope.authError = errorData.data.login;
            }
        }
    );

    $rootScope.chat = ChatRest.query();

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

        $rootScope.fetchOnline();

        $rootScope.connected = true;
    });

    $rootScope.fetchOnline = function () {
        $rootScope.loginsOnline = UserRest.query(
            {
                order_by: "u.rating",
                filter: {
                    online: true
                }
            }
        );
    };

    WebsocketService.addListener('listen_user_in', 'user_in', function (newUser) {
        var existingUser = $rootScope.loginsOnline.searchById(newUser['id']);

        if (!existingUser) {
            $rootScope.loginsOnline.push(
                UserRest.profile(newUser)
            )
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

    WebsocketService.addListener('listener_new_tournament_round', 'new_tournament_round', function (data) {
        console.log('new tournament round', data);
        TournamentRest.get_current_game(
            "",
            {
                "id": data.tournament_id
            },
            function (game) {
                $location.path( '/play/' + game.id );
                AudioService.newGame();
            },
            function (errorData) {
                console.log('error_data', data);
            }
        );
    });

    WebsocketService.checkLag();
}]);

playzoneApp.filter('range', function() {
    return function(input, total) {
        total = parseInt(total);

        for (var i=0; i<total; i++) {
            input.push(i);
        }

        return input;
    };
});

var checkIfUnauthorized = function ($q, $rootScope, $location, $cookies) {
    if ($cookies.get("user_login")) {
        $location.path('/');
    }
};

var checkIfNotMobile = function ($q, $rootScope, $location) {
    if ($rootScope.isMobile) {
        $location.path('/games');
    }
};

var checkIfAuthorized = function ($q, $rootScope, $location, $cookies) {
    if (!$cookies.get("user_login")) {
        $location.path('/auth');
    }
};

var playzoneControllers = angular.module('playzoneControllers', []);

var playzoneServices = angular.module('playzoneServices', []);