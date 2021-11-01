const mix = require('laravel-mix');
const srcDir = './frontend/src';
const publicPath = './web/static';

mix
.disableNotifications()
.autoload({
    jquery: ['$', 'window.jQuery']
})
.options({
    processCssUrls: false
})
.js(srcDir + '/js/login.js', '/dist')
.js(srcDir + '/js/app.js', '/dist')
.sass(srcDir + '/css/app.scss', '/dist').options({
    postCss: [
        require('postcss-css-variables')()
    ]
})
.setPublicPath(publicPath)
.extract()
.version()
.browserSync({
    files: ['./' + srcDir + '/**/*.+(css|js)', '*.html'],
    proxy: 'http://localhost/',
    reload: false
});