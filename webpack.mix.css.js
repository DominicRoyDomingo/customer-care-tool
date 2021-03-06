const mix = require("laravel-mix");

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
  .setPublicPath("public")
  .setResourceRoot("../../../") // Turns assets paths in css relative to css file
  // .options({
  //     processCssUrls: false,
  // })
  .sass("resources/sass/frontend/app.scss", "css/frontend.css")
  .sass("resources/sass/backend/app.scss", "css/backend.css")
  .sourceMaps();

if (mix.inProduction()) {
  mix.version().options({
    // Optimize JS minification process
    terser: {
      cache: true,
      parallel: true,
      sourceMap: true,
    },
  });
} else {
  // Uses inline source-maps on development
  mix.webpackConfig({
    devtool: "eval",
    resolve: {
      alias: {
        '@': path.resolve('resources/') // just to use relative path properly
      }
    }
  });
}
