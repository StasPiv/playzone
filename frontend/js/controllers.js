/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneControllers = angular.module('playzoneControllers', []);

playzoneControllers.controller('TopMenuCtrl', function ($scope) {
    $scope.menu = [
        {
            "label": "Games",
            "url": "/games/",
            "menu": [
                {
                    "label": "My games",
                    "url": "/games/"
                },
                {
                    "label": "All games",
                    "url": "/games/personal/"
                },
                {
                    "label": "TOP 10 games",
                    "url": "/games/personal/?filter_button=OK&top=10"
                }
            ]
        },
        {
            "label": "Tournaments",
            "url": "/tournaments/",
            "menu": [
                {
                    "label": "Personal",
                    "url": "/tournaments/"
                },
                {
                    "label": "Call tournaments",
                    "url": "/tournaments/call/"
                },
                {
                    "label": "Special",
                    "url": "/tournaments/prerecord/"
                },
                {
                    "label": "Top tournaments",
                    "url": "/tournaments/top/"
                },
                {
                    "label": "Team",
                    "url": "/tournaments/team/"
                },
                {
                    "label": "By record",
                    "url": "/tournaments/prerecord/"
                },
                {
                    "label": "Archive",
                    "url": "/tournaments/archive/"
                }
            ]
        },
        {
            "label": "Players",
            "url": "/players/list/",
            "menu": [
                {
                    "label": "Rating-list",
                    "url": "/players/list/"
                },
                {
                    "label": "Search player",
                    "url": "/players/search/"
                },
                {
                    "label": "Teams",
                    "url": "/team/"
                }
            ]
        },
        {
            "label": "Communication", // TODO: counter of messages
            "url": "/team/chat/",
            "menu": [
                {
                    "label": "Common forum",
                    "url": "http://immortalchess.net/forum/forumdisplay.php?f=85"
                },
                {
                    "label": "Team chat", // TODO: counter of messages
                    "url": "/team/chat/"
                },
                {
                    "label": "Ask question",
                    "url": "http://immortalchess.net/forum/private.php?do=newpm&u=87"
                }
            ]
        }
    ];
});

playzoneControllers.controller('LanguageCtrl', ['$translate', '$scope', function ($translate, $scope) {
    $scope.changeLanguage = function (langKey) {
        $translate.use(langKey);
    };
}]);

playzoneControllers.controller('TopRegisterCtrl', function ($scope, $rootScope, $cookies) {
    $scope.logout = function() {
        $cookies.remove("user_login");
        $cookies.remove("user_password");
        $rootScope.user = {};
    }
});

playzoneControllers.controller('HomeCtrl', function ($scope) {
    $scope.isEnableYoutubeGuide = false;
    $scope.mainPossibilities = [
        'The personal game',
        'The personal tournaments',
        'The team tournaments',
        'Miscellaneous time controls',
        'Rating calculations and class promotions',
        'The rest time'
    ];
});

playzoneControllers.controller('RegisterCtrl', function ($scope, $rootScope, $http, $location, UserService) {
    $rootScope.user = {};
    $scope.errors = {};

    $scope.register = function() {
        UserService.register({
            user: $scope.user,
            success: function(data) {
                $scope.errors = {};
                $location.path('/');
            },
            error: function(data) {
                $scope.errors = data.errors;
            }
        });
    }
});

playzoneControllers.controller('AuthCtrl', function ($scope, $rootScope, $http, $location, UserService) {
    $rootScope.user = {};
    $scope.errors = {};

    $scope.auth = function() {
        UserService.auth({
            user: $scope.user,
            success: function(data) {
                $scope.errors = {};
                $location.path('/');
            },
            error: function(data) {
                $scope.errors = data.errors;
            }
        });
    }
});