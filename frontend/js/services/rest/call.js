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
                isArray:true
            }
        }
    );
});