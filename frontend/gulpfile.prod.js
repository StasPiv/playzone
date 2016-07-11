'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var minify = require('gulp-minify');
var concat = require('gulp-concat-util');

var tasks = ['styles', 'compressJs'];

gulp.task('default', tasks);

gulp.task('watch', function () {
    gulp.watch('sass/**/*.scss', ['styles']);
    gulp.watch('js/**/*.js', ['compressJs']);
});

gulp.task('styles', function () {
    gulp.src('sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./css/'));
});

gulp.task('compressJs', function() {
    gulp.src([
        'bower_components/jquery/dist/jquery.min.js',
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
        'bower_components/angular/angular.min.js',
        'bower_components/angular-route/angular-route.min.js',
        'bower_components/angular-resource/angular-resource.min.js',
        'bower_components/angular-cookies/angular-cookies.min.js',
        'bower_components/angular-translate/angular-translate.min.js',
        'bower_components/angular-translate-loader-url/angular-translate-loader-url.min.js',
        'bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.min.js',
        'bower_components/angular-translate-storage-local/angular-translate-storage-local.min.js',
        'bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.min.js',
        'bower_components/angular-websocket/angular-websocket.min.js',
        'bower_components/angular-local-storage/dist/angular-local-storage.min.js',
        "js/chessboard-0.3.0.min.js",
        "js/chess.min.js"
    ])
        .pipe(concat('lib.js'))
        .pipe(concat.header('// file: <%= file.path %>\n'))
        .pipe(concat.footer('\n// end\n'))
        .pipe(gulp.dest('dist'));
    
    gulp.src([
        "js/app.js",
        "js/config/translation.js",
        "js/config/route.js",
        "js/controllers/top_menu.js",
        "js/controllers/language.js",
        "js/controllers/top_register.js",
        "js/controllers/register.js",
        "js/controllers/home.js",
        "js/controllers/auth.js",
        "js/controllers/games.js",
        "js/controllers/call.js",
        "js/controllers/play.js",
        "js/controllers/online.js",
        "js/controllers/myprofile.js",
        "js/controllers/profile.js",
        "js/controllers/user_archive.js",
        "js/controllers/tournament.js",
        "js/controllers/tournaments.js",
        "js/controllers/webrtc_share.js",
        "js/directives/drop_down_menu.js",
        "js/directives/open_popup.js",
        "js/directives/chess_board_legal.js",
        "js/directives/play_chess_board.js",
        "js/directives/chess_timer.js",
        "js/directives/chat.js",
        "js/directives/pgn_notation.js",
        "js/directives/user_link.js",
        "js/directives/games_list.js",
        "js/directives/tournaments_list.js",
        "js/directives/tournament_table.js",
        "js/directives/tournament_record.js",
        "js/directives/challenges.js",
        "js/directives/game_params.js",
        "js/services/environment.js",
        "js/services/cookie.js",
        "js/services/api.js",
        "js/services/websocket.js",
        "js/services/webrtc.js",
        "js/services/localstorage.js",
        "js/services/audio.js",
        "js/services/setting.js",
        "js/services/rest/call.js",
        "js/services/rest/game.js",
        "js/services/rest/user.js",
        "js/services/rest/chat.js",
        "js/services/rest/pgn.js",
        "js/services/rest/tournament.js",
        "js/services/rest/log.js",
        "js/utils.js"
    ])
        .pipe(concat('app.js'))
        .pipe(concat.header('// file: <%= file.path %>\n'))
        .pipe(concat.footer('\n// end\n'))
        .pipe(gulp.dest('dist'));
});