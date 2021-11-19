const mix = require('laravel-mix');
const srcDir = './frontend/src';
const publicPath = './web/static';

mix
.webpackConfig({
    externals: {
        jquery: "jQuery"
    }
})
.disableNotifications()
.options({
    processCssUrls: false
})
.setPublicPath(publicPath)
.copy('./node_modules/jquery/dist/jquery.min.js', publicPath + '/dist/js')
.copyDirectory(srcDir + '/images/*', publicPath + '/dist/images')
.js(srcDir + '/js/login.js', '/dist/js')
.js(srcDir + '/js/app.js', '/dist/js')
.sass(srcDir + '/css/app.scss', '/dist/css').options({
    postCss: [
        require('postcss-css-variables')()
    ]
})
.extract()
.version()
.browserSync({
    files: ['./' + srcDir + '/**/*.+(css|js)', '*.html'],
    proxy: 'http://localhost/',
    reload: false
});