'use strict';

module.exports = function (config) {
    config.set({

        basePath : '../',

        files : [
            'frontend/bower_components/angular/angular.js',
            'frontend/bower_components/angular-mocks/angular-mocks.js',
            'frontend/bower_components/angular-cookies/angular-cookies.js',
            'frontend/bower_components/angular-route/angular-route.js',
            'frontend/bower_components/angular-websocket/angular-websocket.js',
            'frontend/bower_components/angular-translate/angular-translate.js',
            'frontend/bower_components/angular-translate-loader-url/angular-translate-loader-url.js',
            'frontend/bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.js',
            'frontend/bower_components/angular-translate-storage-local/angular-translate-storage-local.js',
            'frontend/bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.js',
            'frontend/bower_components/angular-resource/angular-resource.js',
            'frontend/bower_components/jquery/dist/jquery.js',
            'frontend/js/**/*.js',
            'frontend/test/unit/**/*.js'
        ],

        frameworks: ['jasmine'],

        browsers : ['Chrome', 'Firefox'],

        plugins : [
            'karma-chrome-launcher',
            'karma-firefox-launcher',
            'karma-jasmine'
        ],

        junitReporter : {
            outputFile: 'test_out/unit.xml',
            suite: 'unit'
        }

    });
};
