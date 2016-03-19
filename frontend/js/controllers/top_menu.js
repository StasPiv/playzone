/**
 * Created by stas on 16.01.16.
 */
'use strict';

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
        }
    ];
});