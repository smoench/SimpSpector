var gulp   = require('gulp'),
    watch  = require('gulp-watch'),
    concat = require('gulp-concat'),
    sass   = require('gulp-sass')
;

gulp.task('sass', function () {
    return gulp.src('scss/*.scss')
        .pipe(sass())
        .pipe(gulp.dest('.cache/css/'))
    ;
});

gulp.task('styles', ['sass'], function () {
    var files = [
        'bower_components/semantic/dist/semantic.css',
        '.cache/css/*.css'
    ];

    gulp.src(files)
        .pipe(concat('all.css'))
        .pipe(gulp.dest('../web/css/'))
    ;
});

gulp.task('watch', function () {
    gulp.watch('scss/*.scss', ['styles']);
});

gulp.task('default', ['watch'], function() {});
