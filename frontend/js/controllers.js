/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneControllers = angular.module('playzoneControllers', []);

playzoneControllers.controller('TopMenuCtrl', function ($scope) {
    $scope.menu = [
        {
            "label": "Партии",
            "url": "/games/",
            "menu": [
                {
                    "label": "Мои партии",
                    "url": "/games/"
                },
                {
                    "label": "Все партии",
                    "url": "/games/personal/"
                },
                {
                    "label": "TOP 10 партий",
                    "url": "/games/personal/?filter_button=OK&top=10"
                }
            ]
        },
        {
            "label": "Турниры",
            "url": "/tournaments/",
            "menu": [
                {
                    "label": "Личные",
                    "url": "/tournaments/"
                },
                {
                    "label": "Турниры вызова",
                    "url": "/tournaments/call/"
                },
                {
                    "label": "Особые",
                    "url": "/tournaments/prerecord/"
                },
                {
                    "label": "Топ турниры",
                    "url": "/tournaments/top/"
                },
                {
                    "label": "Командные",
                    "url": "/tournaments/team/"
                },
                {
                    "label": "По записи",
                    "url": "/tournaments/prerecord/"
                },
                {
                    "label": "Архив",
                    "url": "/tournaments/archive/"
                }
            ]
        },
        {
            "label": "Игроки",
            "url": "/players/list/",
            "menu": [
                {
                    "label": "Рейтинг-лист",
                    "url": "/players/list/"
                },
                {
                    "label": "Найти игрока",
                    "url": "/players/search/"
                },
                {
                    "label": "Команды",
                    "url": "/team/"
                }
            ]
        },
        {
            "label": "Общение (+0)", // TODO: counter of messages
            "url": "/team/chat/",
            "menu": [
                {
                    "label": "Общий форум",
                    "url": "http://immortalchess.net/forum/forumdisplay.php?f=85"
                },
                {
                    "label": "Командный чат (+0)", // TODO: counter of messages
                    "url": "/team/chat/"
                },
                {
                    "label": "Задать вопрос",
                    "url": "http://immortalchess.net/forum/private.php?do=newpm&u=87"
                }
            ]
        }
    ];
});

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
        'Игра один на один',
        'Игра в личных турнирах',
        'Игра в командных турнирах',
        'Различные игровые контроли времени',
        'Подсчет рейтинга и продвижение по классам',
        'Отпускное время'
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