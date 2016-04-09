/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneServices.factory('TournamentRest', function($resource, ApiService) {
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
                url: ApiService.base_url + 'tournament/list',
                isArray:true
            },
            record: {
                method: "POST",
                url: ApiService.base_url + 'tournament/:id/record'
            }
        }
    );
});