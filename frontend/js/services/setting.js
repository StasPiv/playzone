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

            var setting = $rootScope.user.settings[settingName];

            if (setting.type === 'checkbox') {
                return !!parseInt(setting.value);
            }

            return setting.value;
        }
    };
});
