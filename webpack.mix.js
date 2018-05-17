let mix = require('laravel-mix')

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

mix
  .js('resources/assets/js/app.js', 'public/js')
  .sourceMaps()
  .extract(['vue'])

  .sass('resources/assets/sass/app.scss', 'public/css')

  .copyDirectory('resources/assets/static', 'public/static')

  .copy('resources/assets/manifest.json', 'public')
  .copy('resources/assets/serviceWorker.js', 'public')

  .browserSync({
    proxy: 'https://chatter.oo'
  })

// When in produciton, create versioning of the JavaScript files,
// so that the browser reloads them
// see: https://laravel.com/docs/master/mix#versioning-and-cache-busting
if (mix.inProduction()) {
  mix.version()
}
