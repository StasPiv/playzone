/**
 * Created by stas on 18.04.16.
 */
'use strict';

playzoneServices.factory('SettingService', function($rootScope) {
    return {
        getSetting: function (settingName) {
            if (!$rootScope.user.settings || !$rootScope.user.settings[settingName]) {
                return null;
            }

            return $rootScope.user.settings[settingName].value;
        }
    };
});
