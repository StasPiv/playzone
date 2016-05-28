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
            get: {
                url: ApiService.base_url + 'tournament/:id'
            },
            record: {
                method: "POST",
                url: ApiService.base_url + 'tournament/:id/record'
            },
            unrecord: {
                method: "DELETE",
                url: ApiService.base_url + 'tournament/:id/unrecord'
            },
            get_current_game: {
                method: "GET",
                url: ApiService.base_url + 'tournament/:id/currentgame'
            }
        }
    );
});