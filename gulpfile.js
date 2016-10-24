// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var sass = require('gulp-ruby-sass');
var path = require('path');
var changed = require('gulp-changed');
var notify = require("gulp-notify");
var ftp = require('gulp-ftp');
var spritesmith = require('gulp.spritesmith');

//General
var themeName = 'default-bootstrap';
var projectDir = path.resolve(__dirname);

var paths = {
    prestashopImgDir: './themes/default-bootstrap/img/',
    prestashopSpriteFiles: './themes/default-bootstrap/img/sprite/*.png',
    prestashopSassDir: './themes/default-bootstrap/sass/',
    prestashopSassFiles: './themes/default-bootstrap/sass/**/*.scss',
    prestashopCssDir: './themes/default-bootstrap/css'
};
var sassConfig = {
    style: 'expanded',
    compass: true,
    loadPath: [projectDir + '/themes/'+ themeName +'/sass']
};

var ftpConnect = {
    host: 'pinguin1.ftp.ukraine.com.ua',
    user: 'pinguin1_auto',
    pass: 'j2c13o1i'
};

/*
* Custom routine to cancel gulp when jshint is failed
* (Currently not implemented in gulp-jshint :/)
*/
var map = require('map-stream');
var exitOnJshintError = map(function (file, cb) {
    if (!file.jshint.success) {
        console.error('jshint failed');
        process.exit(1);
    }
});

/* Task
* Compile our prestashop SASS files
*/
gulp.task('sass', function() {
    ftpConnect.remotePath = '/themes/default-bootstrap/css';
    return gulp.src(paths.prestashopSassFiles)
        .pipe(changed(paths.prestashopCssDir,{ extension: '.css' }))
        .pipe(sass(sassConfig))
        .pipe(ftp(ftpConnect))
        .pipe(gulp.dest(paths.prestashopCssDir))
        .pipe(notify("SASS Done!"));
});


gulp.task('sass:all', function() {
    ftpConnect.remotePath = '/themes/default-bootstrap/css';
    return gulp.src(paths.prestashopSassFiles)
        .pipe(sass(sassConfig))
        .pipe(ftp(ftpConnect))
        .pipe(gulp.dest(paths.prestashopCssDir));
});

gulp.task('sprite', function() {
    var spriteData =
        gulp.src(paths.prestashopSpriteFiles) // путь, откуда берем картинки для спрайта
            .pipe(spritesmith({
                imgName: 'sprite.png',
                cssName: '_sprite.scss',
                algorithm: 'binary-tree',
                padding: 5,
            }));

    spriteData.img.pipe(gulp.dest(paths.prestashopImgDir)); // путь, куда сохраняем картинку
    spriteData.css.pipe(gulp.dest(paths.prestashopSassDir)); // путь, куда сохраняем стили
    return spriteData.pipe(notify("Sprite Done!"));
});

/* Task
* Watch Files For Changes
*/
gulp.task('watch', function() {
    gulp.watch(paths.prestashopSassFiles, ['sass']);
    gulp.watch(paths.prestashopSpriteFiles, ['sprite']);
});

// Default Task
gulp.task('default', ['sass:all', 'watch']);