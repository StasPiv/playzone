/**
 * Created by stas on 03.05.16.
 */
'use strict';

playzoneControllers.directive('playzoneChat', function ($rootScope, WebsocketService, GameRest, ChatRest, UserRest, SettingService) {
    return {
        restrict: 'E',
        link: function(scope, element) {
            
            scope.isChatDisplayed = SettingService.getSetting('Show chat');

            scope.toggleChatMessages = function () {
                $(element).find('.chat').slideToggle();
                scope.isChatDisplayed = !scope.isChatDisplayed;

                var value = scope.isChatDisplayed ? 1 : 0;
                UserRest.edit_setting(
                    {
                        setting_id: 'show-chat',
                        value: value
                    },
                    function () {
                        $rootScope.user.settings['Show chat'] = {
                            value: value
                        };
                    }
                );
            };

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

            $(element).on('keyup', '.message', function (event) {
                if(event.which === 13) {
                    scope.addMessage();
                }
            });

            scope.addMessage = function () {
                if ($rootScope.user.banned) {
                    return;
                }

                var messageInput = $(element).find(".message");
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
        templateUrl: 'partials/chat.html?rand=' + Math.random()
    }
});