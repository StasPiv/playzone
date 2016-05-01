/**
 * Created by stas on 14.04.16.
 */
'use strict';

playzoneControllers.controller('MyProfileCtrl', function ($scope, $rootScope, UserRest) {
    $scope.user_setting = {};
    
    while (!$rootScope.user || !$rootScope.user.settings) {
    }

    $scope.user_setting['Piece type'] = $rootScope.user.settings['Piece type'].value;

    $scope.changeSetting = function (settingId, type, settingName) {
        var value = $scope.user_setting[settingName];

        if (type === 'checkbox') {
            value = value != 1 ? 0 : 1;
        }
        
        UserRest.edit_setting(
            {
                setting_id: settingId,
                value: value
            },
            function () {
                $rootScope.user.settings[settingName].value = value;
            }
        );
    }
});