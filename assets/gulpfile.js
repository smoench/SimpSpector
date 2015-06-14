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
        'node_modules/semantic-ui-css/semantic.css',
        'node_modules/prismjs/themes/prism.css',
        'node_modules/prismjs/plugins/line-highlight/prism-line-highlight.css',
        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.css',
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
        'node_modules/jquery/dist/jquery.js',
        'node_modules/semantic-ui-css/semantic.js',
        'node_modules/prismjs/components/prism-core.js',
        'node_modules/prismjs/components/prism-markup.js',
        'node_modules/prismjs/components/prism-twig.js',
        'node_modules/prismjs/components/prism-clike.js',
        'node_modules/prismjs/components/prism-php.js',
        'node_modules/prismjs/plugins/line-highlight/prism-line-highlight.js',
        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.js',
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

gulp.task('default', ['js', 'styles']);
