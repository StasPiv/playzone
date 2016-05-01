/**
 * Created by stas on 01.05.16.
 */

'use strict';

playzoneControllers.controller('ProfileCtrl', function ($scope, $routeParams, UserRest) {
    $scope.user = UserRest.profile(
        {
            id: $routeParams.userId
        }
    );
});
