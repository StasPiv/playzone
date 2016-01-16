/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('EnvService', function() {
    return {
        testMode: 0,
        prodMode: 1,
        currentMode : 1
    };
});