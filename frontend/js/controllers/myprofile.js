/**
 * Created by stas on 14.04.16.
 */
'use strict';

playzoneControllers.controller('MyProfileCtrl', function ($scope, $rootScope, UserRest, $cookies, WebsocketService) {
    $scope.user_setting = {};

    $scope.immortalchessUser = {};
    $scope.immortalSuccess = false;

    $scope.immortalAuth = function () {
        UserRest.external_auth(
            $scope.immortalchessUser,
            function () {
                $scope.immortalSuccess = true;
            },
            function () {
                $scope.immortalSuccess = false;
            }
        );
    };

    $rootScope.user
        .$auth({
            login: $rootScope.user ? $rootScope.user.login : $cookies.get("user_login"),
            token: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token"),
            password: $rootScope.user ? $rootScope.user.token : $cookies.get("user_token")
        }).then(
        function () {
            $scope.user_setting['Piece type'] = 'merida';
            $scope.lag = $rootScope.user.lag;

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
            };

            $scope.checkLag = function () {
                $scope.checkingLag = true;
                WebsocketService.checkLag(
                    function (lag) {
                        $rootScope.user.lag = $scope.lag = lag;
                        $scope.checkingLag = false;
                    }
                )
            };
            
            $scope.checkLag();
        }
    );
});