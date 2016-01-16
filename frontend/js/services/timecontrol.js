/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneServices.factory('TimeControlService', function($http, ApiService) {
    return {
        initTimeControls: function($scope) {
            $http.get(ApiService.get_time_controls)
                .then(
                function(response)
                {
                    $scope.timecontrols = response.data.data;

                    $scope.timecontrolsHashMap = [];

                    angular.forEach($scope.timecontrols, function(value, key) {
                        $scope.timecontrolsHashMap[value.id] = value.name;
                    });
                }
            );
        }
    };
});