const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const defaultConfig = require("./node_modules/@wordpress/scripts/config/webpack.config");

module.exports = {
	...defaultConfig,

	optimization: {
		runtimeChunk: false,
		minimizer: [
			new TerserPlugin( {
				cache: true,
				parallel: true,
				extractComments: false,
				terserOptions: {
					output: {
						comments: false,
					}
				}
			} )
		]
	},

	context: path.resolve( __dirname, 'assets/js/src' ),

	entry: {
		'editor': './editor.js',
	},

	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: '[name].js',
	},

	module: {
		...defaultConfig.module,
		rules: [
			...defaultConfig.module.rules,
			{
				test: /\.css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					'css-loader',
					'postcss-loader',
				]
			},
		]
	},

	plugins: [
		...defaultConfig.plugins,
		new MiniCssExtractPlugin( {
			filename: '../css/[name].css',
		} ),
	],
};
