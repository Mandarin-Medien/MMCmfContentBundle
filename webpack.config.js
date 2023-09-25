var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('Resources/public/dist/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/bundles/mmcmfcontent/dist')

    // will create web/build/app.js and web/build/app.css
    .addEntry('js/mmcmfcontent', './Resources/src/js/core.js')
    .addStyleEntry('css/mmCmfContentEditor', './Resources/src/less/mmCmfContentEditor.less')
    //.addStyleEntry('css/mmCmfContentEditor_bootstrap', './Resources/src/less/mmCmfContentEditor_bootstrap.less')

    // allow less files to be processed
    .enableLessLoader(function(options) {
        // https://github.com/webpack-contrib/less-loader#examples
        // http://lesscss.org/usage/#command-line-usage-options
        options.relativeUrls = true;
    })

    .configureBabel(function(config) {
        //config.loader = 'babel-loader';
        config.presets.push('stage-1');

    })

    .setManifestKeyPrefix('dist/')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();