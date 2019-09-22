var gulp = require('gulp'),
    gulps = require('gulp-series'),
    replace = require('gulp-replace'),
    exec = require('child_process').exec,
    secret;

gulps.registerTasks({
    migrate: (function(cb) {
        exec('php artisan migrate:fresh', function(err) {
            if( err) {
                console.log(err);
            }
            cb(err);
        });
    }),
    symlinks: (function(cb) {
        exec('php artisan storage:link', function(err) {
            if( err) {
                console.log(err);
            }
            cb(err);
        });
    }),
    passport: (function(cb) {
        exec('php artisan passport:install', function(err) {
            if( err) {
                console.log(err);
            }
            cb(err);
        });
    }),
    passportSecret: (function(cb) {
        exec('php artisan passportSecret', function(err, stdout) {
            if( err) {
                console.log(err);
            } else {
                secret = stdout;
                console.log(secret);
            }
            cb(err);
        });
    }),
    setEnvSecret: function() {
        return gulp.src(['.env']).
            pipe(replace(/PASSWORD_CLIENT_SECRET=(.*)/g, 'PASSWORD_CLIENT_SECRET=' + secret))
            .pipe(gulp.dest(''));
    }
});

gulps.registerSeries('install', [
    'migrate',
    'symlinks',
    'passport',
    'passportSecret',
    'setEnvSecret'    
]);
