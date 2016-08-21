/**
 * Created by stas on 08.05.16.
 */
'use strict';

playzoneControllers.directive('userLink', function () {
    return {
        restrict: 'E',
        link: function(scope, element) {

        },
        transclude: true,
        scope: {
            user: '=',
            withoutrating: '='
        },
        templateUrl: 'partials/user_link.html?v=2'
    }
});