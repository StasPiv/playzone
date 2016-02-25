var gulp = require('gulp');

gulp.task('default', ['watch']);

var sass = require('gulp-sass');

gulp.task('watch', function () {
    gulp.watch('sass/**/*.scss', ['styles']);
});

gulp.task('styles', function () {
    gulp.src('sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./css/'));
});
