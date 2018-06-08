/*
|--------------------------------------------------------------------------
| Mix Asset Management
|--------------------------------------------------------------------------
|
| Mix provides a clean, fluent API for defining some Webpack build steps
| for your Laravel application. By default, we are compiling the Sass
| file for the application as well as bundling up all the JS files.
|
*/
let mix = require('laravel-mix')


/**
 * custom webpack configurations
 */
const {
  InjectManifest
} = require('workbox-webpack-plugin')
mix.webpackConfig({
  plugins: [
    new InjectManifest({
      swSrc: './resources/assets/js/sw.js'
    })
  ]
})


mix
  .js('resources/assets/js/app.js', 'public/js')
  .sourceMaps(!mix.inProduction())
  .extract(['vue'])

  .sass('resources/assets/sass/app.scss', 'public/css')

  .copyDirectory('resources/assets/static', 'public/static')

  .copy('resources/assets/manifest.json', 'public')

  .browserSync({
    proxy: 'http://chatter.oo'
  })

// When in production, create versioning of the JavaScript files,
// so that the browser reloads them
// see: https://laravel.com/docs/master/mix#versioning-and-cache-busting
if (mix.inProduction()) {
  mix.version()
}
