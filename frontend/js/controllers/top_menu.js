/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('TopMenuCtrl', function ($scope) {
    $scope.menu = [
        {
            "label": "Games",
            "url": "/#/games/"
        },
        {
            "label": "Players",
            "url": "/#/online/"
        },
        {
            "label": "Tournaments",
            "url": "/#/tournaments/"
        },
        {
            "label": "Problems",
            "url": "/#/problems/"
        }
    ];
});