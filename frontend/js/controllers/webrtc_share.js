/**
 * Created by stas on 11.07.16.
 */
'use strict';

playzoneControllers.controller('WebrtcShareCtrl', function ($scope, WebRTCService) {
    $scope.createRoom = function () {
        WebRTCService.createRoom('test');
    };

    $scope.joinRoom = function () {
        WebRTCService.joinRoom('test');
    };

    $scope.sendScreen = function () {
        var $body = $('body');
        WebRTCService.sendMessage({
            type: 'screen',
            screen: $body.html()
        });
        $body.append('<div class="pointer"></div>');
    };

    WebRTCService.addMessageListener(
        function (webRTCMessage) {
            switch (webRTCMessage.type) {
                case 'screen':
                    $('body').addClass('screen').html(webRTCMessage.screen);

                    var $screen = $('.screen');
                    $screen.on('mousemove', function (e) {
                        WebRTCService.sendMessage({
                            type: 'mousemove',
                            coordinates: {
                                x: e.pageX,
                                y: e.pageY
                            }
                        })
                    });

                    $screen.on('click', function (e) {
                        WebRTCService.sendMessage({
                            type: 'click',
                            coordinates: {
                                x: e.pageX,
                                y: e.pageY
                            }
                        })
                    });

                    break;
                case 'mousemove':
                    $('.pointer').css('top', webRTCMessage.coordinates.y)
                                 .css('left', webRTCMessage.coordinates.x);

                    console.log(webRTCMessage.coordinates);
                    break;
                case 'click':
                    $('.pointer').addClass('clicked');

                    document.elementFromPoint(
                        webRTCMessage.coordinates.x,
                        webRTCMessage.coordinates.y
                    ).click();
                    console.log('click', webRTCMessage.coordinates);
                    break;
            }
        },
        'receive-message'
    );
});