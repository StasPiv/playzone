/**
 * Created by stas on 18.01.16.
 */
'use strict';

playzoneServices.factory('TimecontrolRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        {},
        {
            query: {
                url: ApiService.base_url + 'timecontrols',
                isArray:true
            }
        }
    );
});

