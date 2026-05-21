let dist_folder = '..';
let src_folder = 'src';
let fs = require('fs');
let domain_name = 'steglogistic.loc';
const concat = require('gulp-concat');

let path = {
  build: {
    css: dist_folder + '/css/',
    js: dist_folder + '/js/',
  },
  src: {
    css: [
        src_folder + '/scss/style.scss',
        src_folder + '/scss/front-page.scss',
        src_folder + '/scss/main.scss',
        src_folder + '/scss/other-pages.scss',
    ],
    js: src_folder + '/js/*.js',
  },
  watch: {
    css: src_folder + '/scss/**/*.scss',
    js: src_folder + '/js/**/*.js',
  },
};

let {src, dest} = require('gulp'),
  gulp = require('gulp'),
  fileinclude = require('gulp-file-include'),
  browsersync = require('browser-sync').create(),
  del = require('del'),
  scss = require('gulp-sass')(require('sass')),
  autoprefixer = require('gulp-autoprefixer'),
  group_media = require('gulp-group-css-media-queries'),
  clean_css = require('gulp-clean-css'),
  uglify = require('gulp-uglify-es').default,
  sourcemaps = require('gulp-sourcemaps');

  function browserSyncLocal(params) {
    browsersync.init({
      server: './dist',
      directory: true,
      notify: false
    });
  }

  function js() {
  return src(path.src.js)
    .pipe(fileinclude())
    .pipe(concat('app.js'))
    .pipe(uglify())
    .pipe(dest(path.build.js))
    .pipe(browsersync.stream());
}

function css() {
  return (
    src(path.src.css)
      .pipe(sourcemaps.init())
      .pipe(
        scss({
          outputStyle: 'expanded',
          sourceMap: true
        })
      )
      .pipe(group_media())
      .pipe(autoprefixer())
      .pipe(clean_css())
      .pipe(sourcemaps.write('.'))
      .pipe(dest(path.build.css))
      .pipe(browsersync.stream())
  );
}
function clean(params) {
  //return del(path.clean);
  return del( path.build.css, path.build.js );
}

function watchFiles(params) {
    gulp.watch([path.watch.css], css);
    gulp.watch([path.watch.js], js);
}

function cb() {}

let build = gulp.series(
  //clean,
  gulp.parallel(js, css)
);
let watch = gulp.parallel(watchFiles, build);
let watch_local = gulp.parallel(watchFiles, build, browserSyncLocal);

exports.js = js;
exports.css = css;
exports.build = build;
exports.watch = watch;
exports.local = watch_local;
exports.default = watch;
