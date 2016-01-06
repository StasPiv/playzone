/**
 * Created by stas on 06.01.16.
 */
'use strict';

var playzoneServices = angular.module('playzoneServices',[]);

playzoneServices.factory('ApiService', function() {
    var API_URL = 'http://playzone-test-api.lc';
    return {
        register : API_URL + '/?method=register'
    };
});

playzoneServices.factory('UserService', function($rootScope) {
    return $rootScope.user;
});
