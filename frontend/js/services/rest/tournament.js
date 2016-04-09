/**
 * Created by stas on 09.04.16.
 */
'use strict';

playzoneServices.factory('TournamentRest', function($resource, ApiService) {
    return $resource(
        '',
        null,
        {
            query: {
                url: ApiService.base_url + 'tournament/list',
                isArray:true
            }
        }
    );
});