/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('LanguageCtrl', ['$translate', '$scope', function ($translate, $scope) {
    $scope.changeLanguage = function (langKey) {
        $translate.use(langKey);
    };
}]);