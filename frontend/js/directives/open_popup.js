/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.directive('openPopup', function () {
    var overlay = $('.footer .overlay');
    var sendCallButton = $('.call-form .send-call');

    function hidePopup(popupSelector) {
        overlay.hide();
        $(popupSelector).hide();
    }

    return {
        restrict: 'C',
        link: function(scope, element) {
            element.on('click', function(){
                var popupSelector = $(this).data('open-popup');
                overlay.show();
                $(popupSelector).show();
                overlay.on('click', function() {
                    hidePopup.call(this, popupSelector);
                });
                sendCallButton.on('click', function () {
                    hidePopup.call(this, popupSelector);
                });
                return false;
            });
        }
    }
});