/**
 * Created by stas on 03.05.16.
 */
'use strict';

playzoneControllers.directive('playzoneChat', function (WebsocketService, GameRest, ChatRest) {
    return {
        restrict: 'E',
        link: function(scope, element) {
            var messageInput = $(element).find(".message");

            scope.messageContainer.$promise.then(
                function () {
                    WebsocketService.addListener(
                        "listen_message_container_" + scope.messageContainer.id,
                        "send_message_to_observers_" + scope.chatRoom,
                        function (data) {
                            scope.chat_messages.unshift(data);
                        }
                    );
                    
                    scope.chat_messages = scope.messageContainer.chat_messages ?
                        scope.messageContainer.chat_messages : [];
                }
            );

            messageInput.on('keyup', function (event) {
                if(event.which === 13) {
                    scope.addMessage();
                }
            });

            scope.addMessage = function () {
                var messageText = messageInput.val();

                if (messageText === "") {
                    return;
                }

                WebsocketService.sendMessageToObservers(scope.chatRoom, messageText);

                switch (scope.restContainer) {
                    case 'chat':
                        ChatRest.addMessage(
                            '',
                            {
                                message: messageText
                            },
                            function () {
                                messageInput.val("");
                            }
                        );
                        break;
                    case 'game':
                        GameRest.addMessage(
                            '',
                            {
                                id: scope.restContainerId,
                                message: messageText
                            },
                            function () {
                                messageInput.val("");
                            }
                        );
                        break;
                }
            };
        },
        scope: {
            messageContainer: '=',
            restContainer: '=',
            chatRoom: '=',
            restContainerId: '='
        },
        templateUrl: 'partials/chat.html'
    }
});