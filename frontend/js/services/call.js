/**
 * Created by stas on 17.01.16.
 */
'use strict';

playzoneServices.factory('CallService', function($http, $rootScope, ApiService) {
    return {
        sendCall : function(params) {
            if (!params.call) {
                params.call = {};
            }

            var call = params.call;
            call.login = $rootScope.user.login;
            call.token = $rootScope.user.token;
            var onSuccess = params.success;
            var onError = params.error;
            $http({
                method  : 'POST',
                url     : ApiService.post_call,
                data    : call,
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .success(function(data) {
                    onSuccess(data);
                })
                .error(function(data) {
                    onError(data);
                });
        },
        removeCall : function(params) {
            if (!params.call) {
                params.call = {};
            }

            var call = {};
            call.call_id = params.call.id;
            call.login = $rootScope.user.login;
            call.token = $rootScope.user.token;
            var onSuccess = params.success;
            var onError = params.error;

            $http({
                method  : 'DELETE',
                url     : ApiService.remove_call,
                data    : call
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