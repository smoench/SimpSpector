var gulp        = require('gulp'),
    concat      = require('gulp-concat'),
    sass        = require('gulp-sass'),
    cssCompress = require('gulp-minify-css'),
    uglify      = require('gulp-uglify')
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
        'bower_components/prism/themes/prism.css',
        'bower_components/prism/plugins/line-highlight/prism-line-highlight.css',
        'bower_components/prism/plugins/line-numbers/prism-line-numbers.css',
        '.cache/css/*.css'
    ];

    gulp.src(files)
        .pipe(concat('all.css'))
        .pipe(gulp.dest('../web/css/'))
        .pipe(cssCompress())
        .pipe(concat('all.min.css'))
        .pipe(gulp.dest('../web/css/'))
    ;
});

gulp.task('js', function () {
    var files = [
        'bower_components/jquery/dist/jquery.js',
        'bower_components/semantic/dist/semantic.js',
        'bower_components/prism/components/prism-core.js',
        'bower_components/prism/components/prism-markup.js',
        'bower_components/prism/components/prism-clike.js',
        'bower_components/prism/components/prism-php.js',
        'bower_components/prism/plugins/line-highlight/prism-line-highlight.js',
        'bower_components/prism/plugins/line-numbers/prism-line-numbers.js',
        'js/main.js'
    ];

    gulp.src(files)
        .pipe(concat('all.js'))
        .pipe(gulp.dest('../web/js/'))
        .pipe(uglify())
        .pipe(concat('all.min.js'))
        .pipe(gulp.dest('../web/js/'))
    ;
});

gulp.task('watch', function () {
    gulp.watch('scss/*.scss', ['styles']);
    gulp.watch('js/*.js', ['js']);
});

gulp.task('default', ['watch'], function() {});
