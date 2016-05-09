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
            user: '='
        },
        templateUrl: 'partials/user_link.html'
    }
});