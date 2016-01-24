/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, CallRest, GameRest, WebsocketService) {
    var fetchCallsFromMe = function () {
        $scope.calls_from_me = CallRest.query({type: "from"});
    };

    var fetchCallsToMe = function () {
        $scope.calls_to_me = CallRest.query({type: "to"});
    };

    var fetchCurrentGames = function () {
        $scope.current = GameRest.query({status: "play", user:"me"});
    };

    fetchCallsFromMe();
    fetchCallsToMe();
    fetchCurrentGames();

    $scope.acceptCall = function(call) {
        CallRest.accept({},call, function(response) {
            WebsocketService.sendDataToLogins('call_accept', {}, [call.from_user.login]);
            $scope.current.push(response);
            $scope.calls_to_me.splice( $scope.calls_to_me.indexOf(call), 1 );
        });
    };

    $scope.deleteCall = function(call) {
        call.$delete().then(
            function(response) {
                WebsocketService.sendDataToLogins('call_delete', {}, [call.to_user.login]);
            }
        );
    };

    $scope.declineCall = function(call) {
        call.$decline().then(
            function(response) {
                WebsocketService.sendDataToLogins('call_decline', {}, [call.from_user.login]);
            }
        );
    };

    WebsocketService.addListener("listen_sent_calls", "call_send", function(data) {
        fetchCallsToMe();
    });

    WebsocketService.addListener("listen_accepted_calls", "call_accept", function(data) {
        fetchCallsFromMe();
        fetchCurrentGames();
    });

    WebsocketService.addListener("listen_declined_calls", "call_decline", function(data) {
        fetchCallsFromMe();
    });

    WebsocketService.addListener("listen_deleted_calls", "call_delete", function(data) {
        fetchCallsToMe();
    });
});