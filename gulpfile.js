var gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer');

var input = ['src/admin/css/doppler-for-woocommerce-admin.scss','src/public/css/doppler-for-woocommerce-public.scss'];

gulp.task('sass', function () {
    return gulp
        // Find all `.scss` files from the `stylesheets/` folder
        .src(input)
        .pipe(sourcemaps.init())
        // Run Sass on those files
        .pipe(sass().on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(autoprefixer())
        // Write the resulting CSS in the output folder
        .pipe(gulp.dest(function(file){
            return file.base;
        })); //Transpile to same directory
});

gulp.task('watch',function(){
    gulp.watch(input,gulp.series('sass'));
});