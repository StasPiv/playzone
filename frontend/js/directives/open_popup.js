/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.directive('openPopup', function () {
    var overlay = $('.footer .overlay');
    return {
        restrict: 'C',
        link: function(scope, element) {
            element.on('click', function(){
                var popupSelector = $(this).data('open-popup');
                overlay.show();
                $(popupSelector).show();
                overlay.on('click', function() {
                    $(this).hide();
                    $(popupSelector).hide();
                });
                return false;
            });
        }
    }
});