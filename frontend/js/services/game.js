/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('GameService', function($http, ApiService, $rootScope) {
    return {
        initCallsFromMe: function($scope) {
            $http({
                url: ApiService.get_calls,
                method: "GET",
                params: {
                    login: $rootScope.user.login,
                    token: $rootScope.user.token,
                    status: "call",
                    user: "me",
                    call_type: "from"
                }
            }).then(
                function(response) {
                    $scope.games.call_from_me = response.data.data;
                }
            );
        },
        initCallsToMe: function($scope) {
            $http({
                url: ApiService.get_calls,
                method: "GET",
                params: {
                    login: $rootScope.user.login,
                    token: $rootScope.user.token,
                    status: "call",
                    user: "me",
                    call_type: "to"
                }
            }).then(
                function(response) {
                    $scope.games.call_to_me = response.data.data;
                }
            );
        },
        initCurrentGames: function($scope) {
            $http({
                url: ApiService.get_games,
                method: "GET",
                params: {
                    login: $rootScope.user.login,
                    token: $rootScope.user.token,
                    status: "play",
                    user: "me"
                }
            }).then(
                function(response) {
                    $scope.games.current = response.data.data;
                }
            );
        }
    };
});