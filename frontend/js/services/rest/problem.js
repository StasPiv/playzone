/**
 * Created by stas on 09.08.16.
 */

'use strict';

playzoneServices.factory('ProblemRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        $.extend(
            {
                id:'@id'
            },
            ApiService.getSecurityParams()
        ),
        {
            get_random: {
                method: 'GET',
                url: ApiService.base_url + 'problem/random'
            },
            solve: {
                method: 'POST',
                url: ApiService.base_url + 'problems/:id/solutions'
            }
        }
    );
});