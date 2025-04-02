const mix = require('laravel-mix');
const fs = require("fs");

mix
  .js('resources/js/app.js', 'public/js')
  .vue()
  .sass('resources/sass/app.scss', 'public/css')
  .sass('resources/sass/preloader.scss', 'public/css')
  .postCss('resources/css/app.css', 'public/css', [require('autoprefixer')])
  .alias({
    '@': 'resources/js',
  })
  .webpackConfig({
    stats: {
      children: true,
      warnings: false, // Esto deshabilita todos los warnings
    },
  });
if (mix.inProduction()) {
  mix.version();
}
mix.then(() => {
    const fs = require('fs');
    const gitignorePath = 'public/js/.gitignore';
    if (!fs.existsSync(gitignorePath)) {
        fs.writeFileSync(gitignorePath, '*\n!.gitignore');
    }
});
