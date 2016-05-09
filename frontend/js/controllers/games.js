/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, $rootScope, $location, CallRest, GameRest, WebsocketService, WebRTCService, AudioService, ChatRest) {
    $rootScope.chat = ChatRest.query();

    $scope.calls_from_me = CallRest.query({type: "from"});
    $scope.calls_to_me = CallRest.query({type: "to"});
    $scope.current = GameRest.query({status: "play", user:"all"});

    $scope.acceptCall = function(call) {
        CallRest.accept({},call, function(responseGame) {
            WebsocketService.sendDataToLogins(
                'call_accept',
                {
                    game_id: responseGame.id,
                    call_id: call.id
                },
                []
            );
            $scope.current.push(responseGame);
            $scope.calls_to_me.pullById(call.id);
            AudioService.newGame();
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
                    call.to_user ? [call.to_user.login] : []
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

    $scope.openSendCallToAll = function(call) {
        $('#login_enemy').val("").trigger('change');
    };

    $scope.openSendCallToRobot = function(call) {
        $('#login_enemy').val("Robot").trigger('change');
    };

    WebsocketService.addListener("listen_sent_calls", "call_send", function(data) {
        console.log('data: ', data);

        angular.forEach(data, function(value) {
            if (!$scope.calls_to_me.searchById(value.id)) {
                $scope.calls_to_me.push(new CallRest(value));
            }
        });

        AudioService.newCall();
    });

    WebsocketService.addListener("listen_accepted_calls", "call_accept", function(data) {
        // delete all my calls when somebody accepts my call
        if ($scope.calls_from_me.searchById(data.call_id)) {
            angular.forEach($scope.calls_from_me, function (call) {
                data.call_id !== call.id && $scope.deleteCall(call);
            });
        }

        $scope.calls_from_me.pullById(data.call_id);
        $scope.calls_to_me.pullById(data.call_id);
        $scope.current.push(new GameRest(data.game));

        if (data.game.mine) {
            console.log(data.game);
            $location.path( '/play/' + data.game.id );
            AudioService.newGame();
        }
    });

    WebsocketService.addListener("listen_declined_calls", "call_decline", function(data) {
        $scope.calls_from_me.pullById(data.call_id);
    });

    WebsocketService.addListener("listen_deleted_calls", "call_delete", function(data) {
        $scope.calls_to_me.pullById(data.call_id);
    });

    WebsocketService.addListener("listen_finished_games", "game_finish", function(data) {
        var game = $scope.current.searchById(data.game_id);
        if (game) {
            game.$get();
        }
    });
});