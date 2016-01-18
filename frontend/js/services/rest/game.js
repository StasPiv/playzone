/**
 * Created by stas on 18.01.16.
 */
'use strict';

playzoneServices.factory('GameRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        $.extend(
            {
                id:'@id'
            },
            ApiService.getSecurityParams()
        ),
        {
            query: {
                url: ApiService.base_url + 'game/list',
                transformResponse: ApiService.transformResponse,
                isArray:true
            }
        }
    );
});