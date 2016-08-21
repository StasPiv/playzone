/**
 * Created by stas on 18.01.16.
 */
'use strict';

playzoneServices.factory('CallRest', function($resource, ApiService) {
    return $resource(
        '',
        $.extend(
            {
                id:'@id'
            },
            ApiService.getSecurityParams() // send with login and token
        ),
        {
            query: {
                url: ApiService.base_url + 'call',
                isArray:true
            },
            delete: {
                method: 'DELETE',
                url: ApiService.base_url + 'call/:id/remove'
            },
            accept: {
                method: 'DELETE',
                url: ApiService.base_url + 'call/:id/accept'
            },
            decline: {
                method: 'DELETE',
                url: ApiService.base_url + 'call/:id/decline'
            },
            send: {
                method: 'POST',
                url: ApiService.base_url + 'call/send',
                transformRequest: function (data) {
                    if (!data.rate) {
                        data.rate = false;
                    }
                    if (data.time.base_minutes) {
                        data.time.base = data.time.base_minutes * 1000 * 60;
                    }
                    if (data.time.increment_seconds) {
                        data.time.increment = data.time.increment_seconds * 1000;
                    }
                    return angular.toJson(data);
                }
            }
        }
    );
});