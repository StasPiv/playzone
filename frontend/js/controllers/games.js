/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('GamesCtrl', function ($scope, CallRest, GameRest) {
    $scope.calls_from_me = CallRest.query({type: "from"});
    $scope.calls_to_me = CallRest.query({type: "to"});
    $scope.current = GameRest.query({status: "play", user:"me"});

    $scope.acceptCall = function(call) {
        call.$accept().then(
            function(response) {
                $scope.current.push(response);
                $scope.calls_to_me.splice( $scope.calls_to_me.indexOf(call), 1 );
            }
        );
    };
});