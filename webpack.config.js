var path = require('path');
var webpack = require('webpack');

module.exports = {
	entry: './js/App.ts',
	output: {
		path: path.resolve(__dirname, './js'),
		filename: 'bundle.js'
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				loader: 'ts-loader',
				options: {
					appendTsSuffixTo: [/\.vue$/]
				}
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader'
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

if (process.env.NODE_ENV === 'production') {
	// See: http://vue-loader.vuejs.org/en/workflow/production.html
	module.exports.plugins = (module.exports.plugins || []).concat([
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: '"production"'
			}
		}),
		new webpack.optimize.UglifyJsPlugin({
			sourceMap: false,
			compress: {
				warnings: false
			}
		}),
		new webpack.LoaderOptionsPlugin({
			minimize: true
		})
	]);
}
