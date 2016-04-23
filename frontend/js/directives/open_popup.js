/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.directive('openPopup', function () {
    var overlay = $('.footer .overlay');

    function hidePopup() {
        overlay.hide();
        $('.call-form').hide();
    }

    $('body').on('click', '.footer .overlay, .call-form .btn', function () {
        hidePopup();
    });

    return {
        restrict: 'C',
        link: function(scope, element) {
            element.on('click', function(){
                var popupSelector = $(this).data('open-popup');
                overlay.show();
                $(popupSelector).show();
                overlay.on('click', function() {
                    hidePopup.call(this);
                });
                return false;
            });
        }
    }
});