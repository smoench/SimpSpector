var gulp        = require('gulp'),
    concat      = require('gulp-concat'),
    cssCompress = require('gulp-minify-css'),
    uglify      = require('gulp-uglify')
;


gulp.task('css', function () {
    var files = [
        'node_modules/prismjs/themes/prism.css',
        'node_modules/prismjs/plugins/line-highlight/prism-line-highlight.css',
        'node_modules/prismjs/plugins/line-numbers/prism-line-numbers.css',
        'node_modules/semantic-ui-css/semantic.css',
        'assets/css/*.css'
    ];

    gulp.src(files)
        .pipe(concat('all.css'))
        .pipe(gulp.dest('web/css/'))
        .pipe(cssCompress())
        .pipe(concat('all.min.css'))
        .pipe(gulp.dest('web/css/'))
    ;

    gulp.src(['node_modules/semantic-ui-css/themes/**'])
        .pipe(gulp.dest('web/css/themes'));
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
        'assets/js/main.js'
    ];

    gulp.src(files)
        .pipe(concat('all.js'))
        .pipe(gulp.dest('web/js/'))
        .pipe(uglify())
        .pipe(concat('all.min.js'))
        .pipe(gulp.dest('web/js/'))
    ;
});

gulp.task('watch', ['js', 'css'], function () {
    gulp.watch('assets/css/*.css', ['css']);
    gulp.watch('assets/js/*.js', ['js']);
});

gulp.task('default', ['js', 'css']);
