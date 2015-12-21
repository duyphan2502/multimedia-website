var path = {
    base: './assets/front/',
    baseAdmin: './assets/admin/',
    distPath: './assets/front/dist/',
    distPathAdmin: './assets/admin/dist/',
    commonPath: './assets/js/third_party/',
    bower: './bower_components/',
    npm: './node_modules/',
    core: './assets/core/',
    coreAdminThirdParty: './assets/admin/core/third_party/',
    themeAdminAssets: './theme/admin/assets/'
};

var gulp = require("gulp");
var sass = require("gulp-sass");
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require("gulp-autoprefixer");
var uglify = require("gulp-uglify");
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var plumber = require('gulp-plumber');
var notify  = require('gulp-notify');

/*Min sass*/
gulp.task("sassAdmin", function(){
    gulp.src(path.baseAdmin + 'scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(plumber({
            errorHandler: notify.onError("Error Sass: <%= error.message %>")
        }))
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(autoprefixer({
            browsers: ['> 1%', 'last 2 versions']
        }))
        //.pipe(concat('app.min.css'))
        .pipe(sourcemaps.write('./map'))
        .pipe(gulp.dest(path.baseAdmin + 'css/'));

    /*Concat css*/
    gulp.src(path.base + 'css/style.css')
        .pipe(concat('app.min.css'))
        .pipe(gulp.dest(path.distPathAdmin));
});

/*Min script*/
gulp.task('coreMinJsAdmin', function() {
    gulp.src([
            path.coreAdminThirdParty + 'jquery.min.js',
            path.bower + 'jquery-placeholder/jquery.placeholder.min.js',
            path.bower + 'modernizr/modernizr.js',
            path.bower + 'bootstrap-sass/assets/javascripts/bootstrap.min.js',
            path.coreAdminThirdParty + 'js.cookie.min.js',
            path.coreAdminThirdParty + 'bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js',
            path.coreAdminThirdParty + 'jquery-slimscroll/jquery.slimscroll.min.js',
            path.coreAdminThirdParty + 'jquery.blockui.min.js',
            path.coreAdminThirdParty + 'uniform/jquery.uniform.min.js',
            path.coreAdminThirdParty + 'bootstrap-switch/js/bootstrap-switch.min.js',
            path.coreAdminThirdParty + 'bootstrap-modal/js/bootstrap-modalmanager.js',
            path.coreAdminThirdParty + 'bootstrap-modal/js/bootstrap-modal.js',
            path.bower + 'jquery-validation/dist/jquery.validate.min.js',
            path.bower + 'jquery-validation/dist/additional-methods.min.js'
        ])
        .pipe(concat('core.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(path.distPathAdmin));
});
gulp.task('scriptsAdmin', function() {
    gulp.src([
            path.themeAdminAssets + 'layouts/layout/scripts/layout.js',
            path.baseAdmin + 'js/utility.js',
            path.baseAdmin + 'js/script.js'
        ])
        .pipe(concat('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(path.distPathAdmin));
});
gulp.task('copyFonts', function() {
    gulp.src([
            path.bower + 'bootstrap-sass/assets/fonts/**/*.{ttf,woff,woff2,eof,svg,otf}',
            path.bower + 'font-awesome-sass/assets/fonts/**/*.{ttf,woff,woff2,eof,svg,otf}'
        ])
        .pipe(gulp.dest(path.baseAdmin + 'fonts/'));
});
gulp.task('copyPagesJs', function() {
    gulp.src([
            path.baseAdmin + 'js/pages/**/*.js',
        ])
        .pipe(gulp.dest(path.distPathAdmin + 'pages/'));
});

/*Admin task*/
gulp.task("admin", [
    'sassAdmin',
    'coreMinJsAdmin',
    'scriptsAdmin',
    'copyFonts',
    'copyPagesJs'
]);

gulp.task("watchAdmin", function(){
    gulp.watch(path.baseAdmin + 'scss/**/*.scss', ['sassAdmin']);
    gulp.watch(path.baseAdmin + 'js/**/*.js', ['scriptsAdmin', 'copyPagesJs']);
});