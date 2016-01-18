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
                transformResponse: ApiService.transformResponse,
                isArray:true
            },
            delete: {
                method: 'DELETE',
                url: ApiService.base_url + 'call/:id/remove'
            },
            accept: {
                method: 'PUT',
                url: ApiService.base_url + 'call/:id/accept'
            },
            decline: {
                method: 'DELETE',
                url: ApiService.base_url + 'call/:id/decline'
            },
            send: {
                method: 'POST',
                url: ApiService.base_url + 'call/send',
                transformRequest: function (request) {
                    if (request.data !== undefined) {
                        delete request.data; // remove data because we are calling this from non-call object
                    }
                    return angular.toJson(request);
                }
            }
        }
    );
});