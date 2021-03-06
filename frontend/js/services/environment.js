/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('EnvService', function() {
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    return {
        isMobile: function () {
            return !!isMobile.any();
        },

        isWebRTC: function () {
            return window.RTCPeerConnection;
        },
        
        testMode: 0,
        prodMode: 1,
        currentMode : 1
    };
});