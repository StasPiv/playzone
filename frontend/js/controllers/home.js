/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneControllers.controller('HomeCtrl', function ($scope, GameRest, TournamentRest) {
    $scope.isEnableYoutubeGuide = false;
    $scope.mainPossibilities = [
        'The personal game',
        'The personal tournaments',
        'The team tournaments',
        'Miscellaneous time controls',
        'Rating calculations and class promotions',
        'The rest time'
    ];
    $scope.current = GameRest.query({status: "play", user:"all"});

    $scope.tournaments_new = TournamentRest.query({
        status: "new"
    });

    $scope.tournaments_current = TournamentRest.query({
        status: "current"
    });

    $scope.tournaments_finished = TournamentRest.query({
        status: "end",
        limit: 5
    });
});
