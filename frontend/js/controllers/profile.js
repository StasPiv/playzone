/**
 * Created by stas on 14.04.16.
 */
'use strict';

playzoneControllers.controller('ProfileCtrl', function ($scope, $rootScope, UserRest) {
    $scope.user_setting = {};

    $scope.changeSetting = function (settingId, type, name) {
        var value = $scope.user_setting[settingId];
        
        if (type === 'checkbox') {
            value = value != 1 ? 0 : 1;
        }
        
        UserRest.edit_setting(
            {
                setting_id: settingId,
                value: value
            },
            function () {
                $rootScope.user.settings[name].value = value;
            }
        );
    }
});