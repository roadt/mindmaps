var webpack = require('webpack');

module.exports = {
	entry: './js/Init.ts',
	output: {
		path: __dirname + '/js',
		filename: 'bundle.js'
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				loader: 'ts-loader'
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			}
		]
	},
	resolve: {
		extensions: ['.webpack.js', '.web.js', '.ts', '.js'],
		alias: {
			'vue$': 'vue/dist/vue.esm.js'
		}
	}
};
