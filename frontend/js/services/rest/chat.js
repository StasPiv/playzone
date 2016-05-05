/**
 * Created by stas on 03.05.16.
 */
'use strict';

playzoneServices.factory('ChatRest', function($resource, $rootScope, ApiService, $filter) {
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
                    var chatMessages = [];
                    $.each(
                        angular.fromJson(data),
                        function (index, value) {
                            chatMessages.push({
                               user: {
                                   login: value.login
                               },
                               time: $filter('date')(new Date(value.time), 'yyyy-MM-dd H:mm:ss'),
                               message: value.message
                            });
                        }
                    );

                    return {
                        chat_messages: chatMessages
                    };
                }
            }
        }
    );
});
