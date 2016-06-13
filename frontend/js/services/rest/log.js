/**
 * Created by stas on 13.06.16.
 */
'use strict';

playzoneServices.factory('LogRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        $.extend(
            {
                id:'@id'
            },
            ApiService.getSecurityParams() // send with login and token
        ),
        {
            log: {
                method: 'POST',
                url: ApiService.base_url + 'log'
            }
        }
    );
});