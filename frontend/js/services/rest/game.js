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
                            pgn: window.btoa(data.pgn),
                            time_white: data.time_white,
                            time_black: data.time_black,
                            insufficient_material_white: !!data.insufficient_material_white,
                            insufficient_material_black: !!data.insufficient_material_black
                        }
                    );
                }
            },
            publishPgn: {
                method: 'POST',
                url: ApiService.base_url + 'game/:id/publish'
            },
            publishFen: {
                method: 'POST',
                url: ApiService.base_url + 'game/:id/publish'
            },
            createNewrobot: {
                method: 'POST',
                url: ApiService.base_url + 'game/newrobot'
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
            timeLost: { // TODO: need to implement separate rest method
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
            },
            addMessage: {
                method: 'POST',
                url: ApiService.base_url + 'game/:id/addmessage',
                transformRequest: function (data) {
                    return angular.toJson({
                        message: data.message
                    });
                }
            },
            fix: {
                method: 'PUT',
                url: ApiService.base_url + 'game/:id/fix'
            }
        }
    );
});