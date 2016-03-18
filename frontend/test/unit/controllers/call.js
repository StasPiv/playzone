/**
 * Created by stas on 28.02.16.
 */
'use strict';

describe('call controller', function () {
    var scope, $httpBackend, ctrl;
    beforeEach(module('playzoneApp'));

    beforeEach(inject(function(_$httpBackend_, $rootScope, $routeParams, $controller, ApiService) {
        $httpBackend = _$httpBackend_;
        $httpBackend.expectGET('translations/en.json').respond();
        $httpBackend.expectPOST(ApiService.auth).respond();
        $httpBackend.expectPOST(ApiService.post_call).respond([
            {
                id: 2,
                game: {
                    id: 2
                }
            },
            {
                id: 2,
                game: {
                    id: 2
                }
            }
        ]);

        scope = $rootScope.$new();
        scope.calls_from_me = [];
        ctrl = $controller('CallCtrl', {$scope: scope});
    }));

    it('calls from me should be increased', function () {
        var lengthBefore = scope.calls_from_me.length;
        expect(true).toBe(true);

        var call = {
            player: "Stas",
            color: "w"
        };
        scope.sendCall(call);
        $httpBackend.flush();

        expect(call.player).toBe('');
        expect(scope.calls_from_me.length).toEqual(lengthBefore + 2);
        expect(scope.errors).toEqual({});
    });
});