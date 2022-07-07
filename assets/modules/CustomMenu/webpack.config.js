/*
 * Evo Custom Menu
 * Copyright (c) 2022
 * delphinpro <delphinpro@yandex.ru>
 */

const path = require('path');
const webpack = require('webpack');
// const VueLoaderPlugin = require('vue-loader/lib/plugin');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const ENV = process.env.NODE_ENV ?? 'production';

console.log('BUILD MODE: ' + ENV);

const isDevelopment = ENV === 'development';
const cssLoaderOptions = { url: false };

let webpackConfig = {
    mode     : ENV,
    watch    : isDevelopment,
    cache    : isDevelopment,
    context  : path.resolve(__dirname, 'src'),
    entry    : {
        index: './index.js',
    },
    output   : {
        path      : path.join(__dirname, 'dist'),
        publicPath: '',
        filename  : '[name].bundle.js',
    },
    devtool  : isDevelopment ? 'source-map' : false,
    module   : {
        rules: [
            {
                test: /\.css$/,
                use : [
                    MiniCssExtractPlugin.loader,
                    { loader: 'css-loader', options: cssLoaderOptions },
                ],
            },
            {
                test: /\.scss$/,
                use : [
                    MiniCssExtractPlugin.loader,
                    { loader: 'css-loader', options: cssLoaderOptions },
                    { loader: 'sass-loader' },
                ],
            },
            {
                test   : /\.js$/,
                loader : 'babel-loader',
                exclude: function (file) {
                    return /node_modules/.test(file)
                        //   && !/\.vue\.js/.test(file)
                        ;
                },
            },
            {
                test  : /\.vue$/,
                loader: 'vue-loader',
            },
        ],
    },
    resolve  : {
        modules   : ['node_modules'],
        extensions: ['.js', '.vue'],
        // alias     : {'vue$': 'vue/dist/vue.esm.js'},
    },
    externals: {
        vue: 'Vue',
    },
    plugins  : [
        new webpack.NoEmitOnErrorsPlugin(),

        new VueLoaderPlugin(),

        new MiniCssExtractPlugin({
            filename: '[name].bundle.css',
        }),

        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify(ENV),
        }),
    ],
};

module.exports = webpackConfig;
