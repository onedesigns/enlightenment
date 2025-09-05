const webpack = require('webpack');
const path    = require('path');
const minicss = require('mini-css-extract-plugin');

module.exports = {
    mode: 'development',
    resolve: {
        fallback: {
            'path': require.resolve('path-browserify'),
        },
    },
    entry: {
        style: path.resolve('src', 'scss/style.scss'),
        'assets/css/core-blocks': path.resolve('src', 'scss/core-blocks.scss'),
        'assets/css/jetpack-blocks': path.resolve('src', 'scss/jetpack-blocks.scss'),
        'assets/css/woocommerce-blocks': path.resolve('src', 'scss/woocommerce-blocks.scss'),
        'assets/css/bp-blocks': path.resolve('src', 'scss/bp-blocks.scss'),
        'assets/css/editor-style': path.resolve('src', 'scss/editor-style.scss'),
        'assets/css/block-editor': path.resolve('src', 'scss/block-editor.scss'),
        'assets/css/jetpack': path.resolve('src', 'scss/plugins/jetpack.scss'),
        'assets/css/elementor': path.resolve('src', 'scss/plugins/elementor.scss'),
        'assets/css/woocommerce': path.resolve('src', 'scss/plugins/woocommerce.scss'),
        'assets/css/the-events-calendar': path.resolve('src', 'scss/plugins/the-events-calendar.scss'),
        'assets/css/buddypress': path.resolve('src', 'scss/plugins/buddypress.scss'),
        'assets/css/bbpress': path.resolve('src', 'scss/plugins/bbpress.scss'),
        'assets/css/customize-controls': path.resolve('src', 'scss/customize-controls.scss'),
        'assets/css/editor-panels': path.resolve('src', 'scss/editor-panels.scss'),
        main: path.resolve('src', 'js/main.js'),
        buddypress: path.resolve('src', 'js/buddypress.js'),
        rtmedia: path.resolve('src', 'js/rtmedia.js'),
        'customize-controls': path.resolve('src', 'js/customize-controls.js'),
        'editor-panels': path.resolve('src', 'js/editor-panels.js'),
    },
    output: {
        path: path.resolve(__dirname, 'public'),
        filename: 'assets/js/[name].js',
    },
    plugins: [
        new minicss({
            filename: '[name].css',
        })
    ],
    module: {
        rules: [
            {
                test: /\.scss$/,
                exclude: /node_modules/,
                use: [
                    minicss.loader, // creates style nodes from JS strings
                    'css-loader', // translates CSS into CommonJS
                    'sass-loader', // compiles Sass to CSS, using Node Sass by default
                ],
            },
            {
                test: /\.js?$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.(jpe?g|png|gif|webp)$/i,
                include: path.resolve(__dirname, 'src/images'),
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        outputPath: 'assets/images/',
                        publicPath: '../assets/images/',
                    },
                }],
            },
        ],
    },
}
