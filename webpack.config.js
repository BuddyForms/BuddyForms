const path = require( 'path' );
const webpack = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
// const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blockCSSPlugin = new ExtractTextPlugin( {
    filename: [
        '/gutenberg/components/hello/bf-hello.css/block.css',
        '/gutenberg/components/hello/bf-hello.css/bf-hello-editor.css',
    ]
} );

// Configuration for the ExtractTextPlugin.
const extractConfig = {
    use: [
        { loader: 'raw-loader' },
        {
            loader: 'postcss-loader',
            options: {
                plugins: [ require( 'autoprefixer' ) ],
            },
        },
        {
            loader: 'sass-loader',
            query: {
                outputStyle:
                    'production' === process.env.NODE_ENV ? 'compressed' : 'nested',
            },
        },
    ],
};

module.exports = {
    entry: [
        './gutenberg/components/hello/bf-hello.js',
    ],
    output: {
        path: path.resolve( __dirname )+'/gutenberg/components/hello/dist',
        filename: 'bf-hello.js',
    },
    watch: true,
    devtool: 'cheap-eval-source-map',
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                },
            }
        ],
    },
    plugins: [
        blockCSSPlugin,
        // new BrowserSyncPlugin({
        //   // Load localhost:3333 to view proxied site
        //   host: 'localhost',
        //   port: '3333',
        //   // Change proxy to your local WordPress URL
        //   proxy: 'https://gutenberg.local'
        // })
    ],
};