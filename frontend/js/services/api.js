/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('ApiService', function(EnvService, $rootScope, $location) {
    var API_URL,
        registerUrl,
        authUrl,
        getUsersUrl,
        getCallsUrl,
        getGamesUrl,
        postCallUrl,
        deleteCallUrl,
        acceptCallUrl,
        declineCallUrl;

    switch (EnvService.currentMode) {
        case EnvService.testMode:
            API_URL = 'http://playzone-test-api.lc';
            registerUrl = API_URL + '/?method=register';
            authUrl = API_URL + '/?method=auth';
            getUsersUrl = API_URL + '/?method=getusers';
            getCallsUrl = API_URL + '/?method=getcalls';
            getGamesUrl = API_URL + '/?method=getgames';
            postCallUrl = API_URL + '/?method=postcall';
            deleteCallUrl = API_URL + '/?method=deletecall';
            acceptCallUrl = API_URL + '/?method=acceptcall';
            declineCallUrl = API_URL + '/?method=declinecall';
            break;
        case EnvService.prodMode:
            var host = $location.host();

            if (host.indexOf('.lc') !== -1) {
                host += '/app_dev.php';
            }

            API_URL = 'http://api.' + host + '/';
            registerUrl = API_URL + 'user/register';
            authUrl = API_URL + 'user/auth';
            getUsersUrl = API_URL + 'user/list';
            getCallsUrl = API_URL + 'game/list';
            getGamesUrl = API_URL + 'game/list';
            postCallUrl = API_URL + 'call/send';
            deleteCallUrl = API_URL + 'call/remove';
            acceptCallUrl = API_URL + 'call/accept';
            declineCallUrl = API_URL + 'call/decline';
    }

    return {
        getSecurityParams: function() {
            if (!$rootScope.user || !$rootScope.user.login) {
                return {};
            }
            return {
                login: function () {
                    return $rootScope.user.login.indexOf("@") === -1 ?
                           $rootScope.user.login :
                           window.btoa($rootScope.user.login);
                },
                token: function () {
                    return $rootScope.user.token;
                }
            }
        },
        base_url : API_URL,
        register : registerUrl,
        auth : authUrl,
        get_users : getUsersUrl,
        get_calls : getCallsUrl,
        get_games : getGamesUrl,
        post_call : postCallUrl,
        remove_call : deleteCallUrl,
        accept_call : acceptCallUrl,
        decline_call : declineCallUrl
    };
});