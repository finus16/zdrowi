const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const HtmlWebackPlugin = require('html-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');

module.exports = {
    entry: './src/index.js',
    output: {
        filename: 'main.js',
        path: path.resolve(__dirname, 'dist'),
    },

    plugins: [
        new MiniCssExtractPlugin({ filename: "[name].[contentHash].css" }),
        new HtmlWebackPlugin({ 
            template: "./src/index.html",
            filename: "index.html"
        }),
        new HtmlWebackPlugin({ 
            template: "./src/aktywna-firma.html",
            filename: "aktywna-firma.html"
        }),
        new HtmlWebackPlugin({ 
            template: "./src/kariera-trenera.html",
            filename: "kariera-trenera.html"
        }),
        new CopyPlugin({
            patterns: [
              { from: 'img', to: 'img' }
            ],
          }),
    ],

    module: {

        rules: [

            {
                test: /\.css|s[ac]ss$/,
                use: [
                    MiniCssExtractPlugin.loader, //3. Extract css into files
                    'css-loader',
                    'sass-loader'
                ]
            },

            {
                test: /\.(ttf|eot|svg|woff(2)?)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "file-loader"
            }
        ]
    }
};