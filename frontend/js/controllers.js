/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneApp = angular.module('playzoneApp', []);

playzoneApp.controller('TopMenuCtrl', function ($scope) {
    // TODO: heed to move menu into json file or something else storage
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