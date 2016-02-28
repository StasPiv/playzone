'use strict';

/* jasmine specs for controllers go here */
describe('play controller', function () {
    var scope, $httpBackend, ctrl;
    beforeEach(module('playzoneApp'));
    beforeEach(inject(function(_$httpBackend_, $rootScope, $routeParams, $controller, ApiService) {
        $httpBackend = _$httpBackend_;
        $httpBackend.expectGET('translations/en.json').respond();
        $httpBackend.expectPOST(ApiService.auth).respond();
        var gameData = function () {
            return {
                status: "play"
            }
        };
        var gameId = '666';
        $httpBackend.expectGET(ApiService.base_url + 'game/' + gameId).respond(gameData());

        $routeParams.gameId = gameId;
        scope = $rootScope.$new();
        ctrl = $controller('PlayCtrl', {$scope: scope});
    }));

    it('should fetch game', function() {
        // before rest request game is not filled
        expect(scope.game.status).not.toBe('play');
        $httpBackend.flush();

        // and now it becomes filled
        expect(scope.game.status).toBe('play');
    });
});
