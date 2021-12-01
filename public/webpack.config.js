const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');
module.exports = {
  entry: {
    sms: path.resolve(__dirname, 'js/angular/sms/main.js')
  },
  devtool: 'eval-source-map',
  plugins: [
    new CleanWebpackPlugin([
      path.resolve(__dirname, 'js/angular/sms/dist/')
    ],{
      verbose:  true
    })
  ],
  output: {
    filename: '[name].[chunkhash].min.js',
    path: path.resolve(__dirname, 'js/angular/sms/dist/')
  }
}