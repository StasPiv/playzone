/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('CookieService', function($cookies) {
    return {
        rememberUser: function (user) {
            var expireDate = new Date();
            expireDate.setDate(expireDate.getDate() + 365*20);

            $cookies.put('user_login', user.login, {'expires': expireDate});
            $cookies.put('user_password', user.password, {'expires': expireDate});
        }
    };
});