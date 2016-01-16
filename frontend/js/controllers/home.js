/**
 * Created by stas on 16.01.16.
 */
'use strict';

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
