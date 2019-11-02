const path = require('path');
const { CleanWebpackPlugin: CleanPlugin } = require('clean-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');

const plugins = [new CleanPlugin()];

if (process.env.NODE_ENV !== 'development') {
  plugins.push(
    new CompressionPlugin({
      include: /.(js|css)$/,
      deleteOriginalAssets: true,
    })
  );
}

module.exports = {
  entry: {
    main: './resources/js/main.js',
    print: './resources/js/print.js',
    'sign-in': './resources/js/sign-in.js',
  },

  output: {
    path: path.resolve(__dirname, './public/assets/js/'),
    filename: '[name].js',
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: 'babel-loader',
      },
    ],
  },

  plugins,
};
