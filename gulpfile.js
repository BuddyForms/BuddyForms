var gulp = require('gulp');
var cssPrefix = require('gulp-css-prefix');
var sass = require('gulp-sass');
var cleancss = require('gulp-clean-css');
var csscomb = require('gulp-csscomb');
var rename = require('gulp-rename');
var autoprefixer = require('gulp-autoprefixer');

var paths = {
  source: './assets/src/*.scss',
};

gulp.task('prefix', function() {
  gulp.src('./assets/css/buddyforms.css')
      .pipe(cssPrefix('buddyforms-'))
      .pipe(gulp.dest('./assets/css'));
  gulp.src('./assets/css/buddyforms-admin.css')
      .pipe(cssPrefix('buddyforms-'))
      .pipe(gulp.dest('./assets/css'));
});

gulp.task('watch', function() {
  gulp.watch('./assets/src/**/*.scss', ['build-spectre', 'prefix']);
  gulp.watch('../spectre/src/**/*.scss', ['build-spectre', 'prefix']);
});

gulp.task('build-spectre', function() {
  gulp.src(paths.source)
    .pipe(sass({outputStyle: 'compact', precision: 10})
      .on('error', sass.logError)
    )
    .pipe(autoprefixer())
    .pipe(csscomb())
    .pipe(gulp.dest('./assets/css'))
    .pipe(cleancss())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('./assets/css'));
});