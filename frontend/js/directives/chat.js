/**
 * Created by stas on 03.05.16.
 */
'use strict';

playzoneControllers.directive('playzoneChat', function (WebsocketService, GameRest) {
    return {
        restrict: 'E',
        link: function(scope, element) {
            var messageInput = $(element).find(".message");

            scope.messageContainer.$promise.then(
                function () {
                    WebsocketService.addListener(
                        "listen_message_container_" + scope.messageContainer.id,
                        "send_message_to_observers_" + scope.messageContainer.id,
                        function (data) {
                            scope.chat_messages.push(data);
                        }
                    );
                    
                    scope.chat_messages = scope.messageContainer.chat_messages ? scope.messageContainer.chat_messages : [];
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

                WebsocketService.sendMessageToObservers(scope.messageContainer.id, messageText);

                GameRest.addMessage({
                    message: messageText,
                    id: scope.messageContainer.id
                },
                    function (data) {
                        messageInput.val("");
                    }
                );
            };
        },
        scope: {
            messageContainer: '='
        },
        templateUrl: 'partials/chat.html'
    }
});