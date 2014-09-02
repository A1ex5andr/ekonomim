/*
$  npm install browserify vinyl-source-stream gulp-streamify gulp-uglify gulp-sass gulp-autoprefixer gulp-livereload --save-dev
*/

var gulp        = require('gulp'),                      // main gulp file
    browserify  = require('browserify'),                // to process JS
    source      = require('vinyl-source-stream'),      // to translate from browserify to what gulp understands
    streamify   = require('gulp-streamify'),           // buffer all content to use it in uglify
    uglify      = require('gulp-uglify'),              // minificate js files
    sass        = require('gulp-sass'),                // thanks GOD for sass ))
    prefix      = require('gulp-autoprefixer'),         // to use actual prefixes
    livereload  = require('gulp-livereload');           // to use liveReload in chrome

//variables
var outputDir = '../ubergrid-child/';
var AUTOPREFIXER_BROWSERS = [
    'ie >= 8',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4.4',
    'bb >= 10'
];

//gulp.task('js', function () {
//    return browserify('./js/main.js')
//        .bundle()
//        .pipe(source('bundle.js'))
//        .pipe(streamify(uglify()))
//        .pipe(gulp.dest(outputDir + '/js'));
//});

gulp.task ('sass', function() {
    var config = {};
    // development
    //config.sourceComments = 'map';
    // production
    //config.outputStyle = 'compressed';

    return gulp.src('sass/style.scss')
        .pipe(sass(config))
        .pipe(prefix(AUTOPREFIXER_BROWSERS))
        .pipe(gulp.dest(outputDir));
});

gulp.task('watch', function() {
    //gulp.watch('src/js/**/*.js', ['js']);
    gulp.watch('sass/**/*.scss', ['sass']);
    livereload.listen();                                        // Create LiveReload server
    gulp.watch(['../ubergrid-child/**']).on('change', livereload.changed);   // Watch any files in dist/, reload on change
});

gulp.task('default', ['sass', 'watch']);