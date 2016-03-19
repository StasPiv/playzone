/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, $location, CallRest, GameRest, WebsocketService, WebRTCService) {
    $scope.calls_from_me = CallRest.query({type: "from"});
    $scope.calls_to_me = CallRest.query({type: "to"});
    $scope.current = GameRest.query({status: "play", user:"all"});

    $scope.acceptCall = function(call) {
        CallRest.accept({},call, function(responseGame) {
            WebsocketService.sendDataToLogins(
                'call_accept',
                {
                    game_id: responseGame.id,
                    login: call.from_user.login,
                    call_id: call.id
                },
                [call.from_user.login]
            );
            WebsocketService.sendDataToLogins(
                'call_delete',
                {
                    call_id: call.id
                },
                []
            );
            $scope.current.push(responseGame);
            $scope.calls_to_me.pullById(call.id);
            $location.path( '/play/' + responseGame.id );
        });
    };

    $scope.deleteCall = function(call) {
        var deletedCallId = call.id;
        call.$delete().then(
            function() {
                WebsocketService.sendDataToLogins(
                    'call_delete',
                    {
                        call_id: deletedCallId
                    },
                    [call.to_user ? call.to_user.login : '']
                );
            }
        );
    };

    $scope.declineCall = function(call) {
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

    WebsocketService.addListener("listen_sent_calls", "call_send", function(data) {
        angular.forEach(data, function(value) {
            $scope.calls_to_me.push(new CallRest(value));
        });
    });

    WebsocketService.addListener("listen_accepted_calls", "call_accept", function(data) {
        $scope.calls_from_me.pullById(data.call_id);
        $scope.current.push(new GameRest(data.game));
        $location.path( '/play/' + data.game.id );
    });

    WebsocketService.addListener("listen_declined_calls", "call_decline", function(data) {
        $scope.calls_from_me.pullById(data.call_id);
    });

    WebsocketService.addListener("listen_deleted_calls", "call_delete", function(data) {
        $scope.calls_to_me.pullById(data.call_id);
    });

    // WebRTC
    $scope.calls_from_me.$promise.then(
        function (calls) {
            angular.forEach(calls, function(call) {

            });
        }
    );
});