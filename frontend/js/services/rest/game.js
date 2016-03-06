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
                isArray:true
            },
            get: {
                url: ApiService.base_url + 'game/:id'
            },
            savePgn: {
                method: 'PUT',
                url: ApiService.base_url + 'game/:id/pgn',
                transformRequest: function (data) {
                    return angular.toJson(
                        {
                            pgn: window.btoa(data.pgn)
                        }
                    );
                }
            },
            resign: {
                method: 'PUT',
                url: ApiService.base_url + 'game/:id/resign',
                transformRequest: function (data) {
                    return angular.toJson(
                        {

                        }
                    );
                }
            },
            offerDraw: {
                method: 'PUT',
                url: ApiService.base_url + 'game/:id/offerdraw',
                transformRequest: function (data) {
                    return angular.toJson(
                        {

                        }
                    );
                }
            },
            acceptDraw: {
                method: 'PUT',
                url: ApiService.base_url + 'game/:id/acceptdraw',
                transformRequest: function (data) {
                    return angular.toJson(
                        {

                        }
                    );
                }
            }
        }
    );
});