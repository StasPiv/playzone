/**
 * Created by stas on 17.01.16.
 */
'use strict';

playzoneServices.factory('CallService', function($http, $rootScope, ApiService) {
    return {
        sendCall : function(params) {
            var call = params.call;
            call.color = params.call.color.id;
            call.timecontrol = params.call.timecontrol.id;
            call.login = $rootScope.user.login;
            call.token = $rootScope.user.token;
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'POST',
                url     : ApiService.post_call,
                data    : call,
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                .success(function(data) {
                    onSuccess(data);
                })
                .error(function(data) {
                    onError(data);
                });
        }
    };
});