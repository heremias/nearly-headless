const path = require('path');
const glob = require('glob');
const { exec } = require('child_process');
const globImporter = require('node-sass-glob-importer');
const jsMatchesInTheme = glob.sync(`./patterns/**/*.js`);
// const sassMatchesInTheme = glob.sync(`./patterns/**/*.scss`);
// Add all matches as entry points.
const entry = {
  'vue-components': './src/index.js',
  'script': jsMatchesInTheme,
  'style': `./patterns/style.scss`,
};

module.exports = {
  publicPath: '/profiles/custom/osu_standard/themes/standard_patterns/dist/',
  configureWebpack: {
    entry,
    resolve: {
      alias: {
        '~~': path.resolve(__dirname)
      }
    },
    externals: {
      vue: 'Vue'
    },
    plugins: [
      {
        // Adding this to rebuild Drupal cache on compile
        apply: (compiler) => {
          compiler.hooks.afterEmit.tap('rebuildDrupalCache', (compilation) => {
            exec('echo "running drush cr" && cd ../../../../../.. && drush cr', (err, stdout, stderr) => {
              if (stdout) process.stdout.write(stdout);
              if (stderr) process.stderr.write(stderr);
            });
          })
        }
      }
    ]
  },
  filenameHashing: false,
  chainWebpack: config => {
    const oneOfsMap = config.module.rule('scss').oneOfs.store
    oneOfsMap.forEach( item => {
      item
        .use('sass-resources-loader')
        .loader('sass-resources-loader')
        .options({
          resources: path.resolve(__dirname, 'node_modules/@umarketing/osu-design-system/src/assets/styles/styles.scss')
        })
        .end()
    })
  },
  css: {
    extract: true,
    loaderOptions: {
      sass: {
        sassOptions: {
          importer: globImporter(),
          includePaths: ['./node_modules', path.resolve(__dirname) ]
        }
      }
    }
  },
}
