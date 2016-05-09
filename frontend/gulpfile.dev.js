'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var minify = require('gulp-minify');
var concat = require('gulp-concat-util');

var tasks = ['styles'];

console.log(gulp);

tasks.push('watch');

gulp.task('default', tasks);

gulp.task('watch', function () {
    gulp.watch('sass/**/*.scss', ['styles']);
});

gulp.task('styles', function () {
    gulp.src('sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./css/'));
});