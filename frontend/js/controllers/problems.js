/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('ProblemsCtrl', function ($scope, ProblemRest) {
    $scope.problem = ProblemRest.get_random();
});