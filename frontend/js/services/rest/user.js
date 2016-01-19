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
            register: {
                method: 'POST',
                url: ApiService.base_url + 'user/register'
            }
        }
    );
});