/**
 * Created by stas on 16.01.16.
 */
'use strict';

playzoneApp.config(['$translateProvider',
        function ($translateProvider) {
            // configures staticFilesLoader
            $translateProvider.useStaticFilesLoader({
                prefix: 'translations/',
                suffix: '.json'
            });
            // load 'en' table on startup
            $translateProvider.preferredLanguage('en');
            // remember language
            $translateProvider.useLocalStorage();
        }]
);