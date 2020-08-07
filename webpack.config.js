const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const defaultConfig = require("./node_modules/@wordpress/scripts/config/webpack.config");

module.exports = {
	...defaultConfig,

	optimization: {
		...defaultConfig.optimization,
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
		path: path.resolve( __dirname, 'assets/js/dist' ),
		filename: '[name].js',
	},

	plugins: [
		...defaultConfig.plugins.map( ( plugin ) => {
			if ( plugin.constructor.name === 'MiniCssExtractPlugin' ) {
				plugin.options.filename = '../../css/[name].css';
			}
			return plugin;
		} ),
	],
};
