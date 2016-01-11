/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneControllers = angular.module('playzoneControllers', [])
.directive('dropDownMenu', function () {
    return {
        restrict: 'C',
        link: function(scope, element) {
            element.hover(
                function()
                {
                    $(this).find('.sub-menu').fadeIn(200);
                },
                function()
                {
                    $(this).find('.sub-menu').fadeOut(200);
                }
            );
        }
    }
})
.directive('openPopup', function () {
    var overlay = $('.footer .overlay');
    return {
        restrict: 'C',
        link: function(scope, element) {
            element.on('click', function(){
                var popupSelector = $(this).data('open-popup');
                overlay.show();
                $(popupSelector).show();
                overlay.on('click', function() {
                    $(this).hide();
                    $(popupSelector).hide();
                });
                return false;
            });
        }
    }
});

playzoneControllers.controller('TopMenuCtrl', function ($scope) {
    $scope.menu = [
        {
            "label": "Games",
            "url": "/#/games/",
            "menu": [
                {
                    "label": "My games",
                    "url": "/#/games/"
                },
                {
                    "label": "All games",
                    "url": "/#/games/personal/"
                },
                {
                    "label": "TOP 10 games",
                    "url": "/#/games/personal/?filter_button=OK&top=10"
                }
            ]
        },
        {
            "label": "Tournaments",
            "url": "/#/tournaments/",
            "menu": [
                {
                    "label": "Personal",
                    "url": "/#/tournaments/"
                },
                {
                    "label": "Call tournaments",
                    "url": "/#/tournaments/call/"
                },
                {
                    "label": "Special",
                    "url": "/#/tournaments/prerecord/"
                },
                {
                    "label": "Top tournaments",
                    "url": "/#/tournaments/top/"
                },
                {
                    "label": "Team",
                    "url": "/#/tournaments/team/"
                },
                {
                    "label": "By record",
                    "url": "/#/tournaments/prerecord/"
                },
                {
                    "label": "Archive",
                    "url": "/#/tournaments/archive/"
                }
            ]
        },
        {
            "label": "Players",
            "url": "/#/players/list/",
            "menu": [
                {
                    "label": "Rating-list",
                    "url": "/#/players/list/"
                },
                {
                    "label": "Search player",
                    "url": "/#/players/search/"
                },
                {
                    "label": "Teams",
                    "url": "/#/team/"
                }
            ]
        },
        {
            "label": "Communication", // TODO: counter of messages
            "url": "/#/team/chat/",
            "menu": [
                {
                    "label": "Common forum",
                    "url": "http://immortalchess.net/forum/forumdisplay.php?f=85"
                },
                {
                    "label": "Team chat", // TODO: counter of messages
                    "url": "/#/team/chat/"
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

playzoneControllers.controller('GamesCtrl', function ($scope, TimeControlService) {
    TimeControlService.initTimeControls();
    $scope.timecontrols = {
        "1" : "5+1/10",
        "2" : "10+1/30"
    };

    $scope.users = {
        "1": "Stas",
        "2": "Petro",
        "3": "Dmytro",
        "4": "Vasyl"
    };

    $scope.games = {
        call_from_me: [
            {
                "id": 1,
                "id_timecontrol": 1,
                "id_white": 1,
                "id_black": 2
            },
            {
                "id": 1,
                "id_timecontrol": 2,
                "id_white": 1,
                "id_black": 3
            },
            {
                "id": 1,
                "id_timecontrol": 1,
                "id_white": 1,
                "id_black": 4
            }
        ],
        call_to_me: [
            {
                "id": 1,
                "id_timecontrol": 1,
                "id_white": 1,
                "id_black": 2
            },
            {
                "id": 1,
                "id_timecontrol": 1,
                "id_white": 1,
                "id_black": 3
            },
            {
                "id": 1,
                "id_timecontrol": 1,
                "id_white": 1,
                "id_black": 4
            }
        ],
        current: [
            {
                "id_game": 1,
                "id": 1,
                "id_timecontrol": 1,
                "id_tournament": 1,
                "id_white": 1,
                "id_black": 2,
                "opponent_online": true,
                "id_opponent": 1,
                "my_color": "white",
                "rest_mine": "01:00:32",
                "rest_opponent": "01:00:32",
                "diff": "01:00:32",
                "is_my_move": true,
                "time_last_move": "01:00:32",
                "last_move": "..Qf7-g8"
            },
            {
                "id_game": 2,
                "id": 1,
                "id_timecontrol": 1,
                "id_tournament": 1,
                "id_white": 1,
                "id_black": 2,
                "opponent_online": false,
                "id_opponent": 1,
                "my_color": "black",
                "rest_mine": "01:00:32",
                "rest_opponent": "01:00:32",
                "diff": "01:00:32",
                "is_my_move": false,
                "time_last_move": "01:00:32",
                "last_move": "..Qf7-h1"
            }
        ]
    };
});

playzoneControllers.controller('CallCtrl', function ($scope) {
    $scope.colors = [
        {id: '0', name: 'Random'},
        {id: '1', name: 'White'},
        {id: '2', name: 'Black'}
    ];
});