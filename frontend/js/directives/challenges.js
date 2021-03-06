/**
 * Created by stas on 18.06.16.
 */
'use strict';

playzoneControllers.directive('challenges', function (CallRest, WebsocketService, AudioService, $location, GameRest) {
    return {
        restrict: 'E',
        link: function(scope, element) {
            scope.calls_from_me = CallRest.query({type: "from"});
            scope.calls_to_me = CallRest.query({type: "to"});

            scope.acceptCall = function(call) {
                CallRest.accept({},call, function(responseGame) {
                    WebsocketService.sendDataToLogins(
                        'call_accept',
                        {
                            game_id: responseGame.id,
                            call_id: call.id
                        },
                        []
                    );
                    scope.current && scope.current.push(responseGame);
                    scope.calls_to_me.pullById(call.id);
                    AudioService.newGame();
                    $location.path( '/play/' + responseGame.id );
                });
            };

            scope.deleteCall = function(call) {
                var deletedCallId = call.id;
                call.$delete().then(
                    function() {
                        WebsocketService.sendDataToLogins(
                            'call_delete',
                            {
                                call_id: deletedCallId
                            },
                            call.to_user ? [call.to_user.login] : []
                        );
                        scope.calls_from_me.pullById(call.id);
                    }
                );
            };

            scope.declineCall = function(call) {
                var declinedCallId = call.id;
                call.$decline().then(
                    function() {
                        WebsocketService.sendDataToLogins(
                            'call_decline',
                            {
                                call_id: declinedCallId
                            },
                            [call.from_user.login]
                        );
                    }
                );
            };

            scope.openSendCallToAll = function(call) {
                $('#login_enemy').val("").trigger('change');
            };

            WebsocketService.addListener("listen_sent_calls", "call_send", function(data) {
                console.log('data: ', data);

                scope.calls_to_me = CallRest.query({type: "to"});
            });

            WebsocketService.addListener("listen_accepted_calls", "call_accept", function(data) {
                // delete all my calls when somebody accepts my call
                if (scope.calls_from_me.searchById(data.call_id)) {
                    angular.forEach(scope.calls_from_me, function (call) {
                        data.call_id !== call.id && scope.deleteCall(call);
                    });
                }

                scope.calls_from_me.pullById(data.call_id);
                scope.calls_to_me.pullById(data.call_id);
                scope.current && scope.current.push(new GameRest(data.game));

                if (data.game.mine) {
                    console.log(data.game);
                    $location.path( '/play/' + data.game.id );
                    AudioService.newGame();
                }
            });

            WebsocketService.addListener("listen_declined_calls", "call_decline", function(data) {
                scope.calls_from_me.pullById(data.call_id);
            });

            WebsocketService.addListener("listen_deleted_calls", "call_delete", function(data) {
                scope.calls_to_me.pullById(data.call_id);
            });
        },
        templateUrl: 'partials/challenges.html?v=4'
    }
});