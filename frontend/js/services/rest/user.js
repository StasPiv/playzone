/**
 * Created by stas on 18.01.16.
 */
'use strict';

playzoneServices.factory('UserRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        {},
        {
            auth: {
                method: 'POST',
                url: ApiService.base_url + 'user/auth'
            },
            external_auth: {
                method: 'POST',
                url: ApiService.base_url + 'user/external/auth',
                params: ApiService.getSecurityParams() // send with login and token
            },
            register: {
                method: 'POST',
                url: ApiService.base_url + 'user/register'
            },
            edit_setting: {
                method: 'PATCH',
                url: ApiService.base_url + 'user/:setting_id/setting',
                params: $.extend(
                    {
                        setting_id:'@setting_id'
                    },
                    ApiService.getSecurityParams() // send with login and token
                )
            },
            ping: {
                method: 'PATCH',
                url: ApiService.base_url + 'user/lag',
                params: $.extend(
                    {
                        lag:'@lag'
                    },
                    ApiService.getSecurityParams() // send with login and token
                )
            },
            profile: {
                method: 'GET',
                url: ApiService.base_url + 'user/:id/profile',
                params: $.extend(
                    {
                        id:'@id'
                    },
                    ApiService.getSecurityParams() // send with login and token
                )
            },
            query: {
                method: 'GET',
                url: ApiService.base_url + 'user/list',
                isArray:true
            }
        }
    );
});