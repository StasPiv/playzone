/**
 * Created by stas on 03.05.16.
 */
'use strict';

playzoneServices.factory('ChatRest', function($resource, $rootScope, ApiService) {
    return $resource(
        '',
        $.extend(
            {
                id:'@id'
            },
            ApiService.getSecurityParams() // send with login and token
        ),
        {
            addMessage: {
                method: "POST",
                url: ApiService.base_url + 'chat/message',
                transformRequest: function (data) {
                    return angular.toJson({
                        message: data.message
                    });
                }
            },
            query: {
                method: "GET",
                url: ApiService.base_url + 'chat/messages',
                isArray: false,
                transformResponse: function(data) {
                    return {
                        chat_messages: angular.fromJson(data)
                    };
                }
            }
        }
    );
});
