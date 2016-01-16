/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('ApiService', function(EnvService) {
    var API_URL,
        registerUrl,
        authUrl,
        getTimeControlsUrl,
        getUsersUrl,
        getCallsUrl,
        getGamesUrl;

    switch (EnvService.currentMode) {
        case EnvService.testMode:
            API_URL = 'http://playzone-test-api.lc';
            registerUrl = API_URL + '/?method=register';
            authUrl = API_URL + '/?method=auth';
            getTimeControlsUrl = API_URL + '/?method=gettimecontrols';
            getUsersUrl = API_URL + '/?method=getusers';
            getCallsUrl = API_URL + '/?method=getcalls';
            getGamesUrl = API_URL + '/?method=getgames';
            break;
        case EnvService.prodMode:
            API_URL = 'http://api.playzone-angular.lc/app_dev.php/';
            registerUrl = API_URL + 'user/register';
            authUrl = API_URL + 'user/auth';
            getTimeControlsUrl = API_URL + 'timecontrols';
            getUsersUrl = API_URL + 'user/list';
            getCallsUrl = API_URL + 'game/list';
            getGamesUrl = API_URL + 'game/list';
    }

    return {
        register : registerUrl,
        auth : authUrl,
        get_time_controls : getTimeControlsUrl,
        get_users : getUsersUrl,
        get_calls : getCallsUrl,
        get_games : getGamesUrl
    };
});