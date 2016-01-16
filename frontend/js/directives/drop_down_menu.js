/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.directive('dropDownMenu', function () {
        return {
            restrict: 'C',
            link: function(scope, element) {
                element.hover(
                    function()
                    {
                        $(this).find('.sub-menu').fadeIn(200);
                    },
                    function()
                    {
                        $(this).find('.sub-menu').fadeOut(200);
                    }
                );
            }
        }
    });