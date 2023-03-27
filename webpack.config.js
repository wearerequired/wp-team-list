const path = require( 'path' );
const RtlCssPlugin = require( 'rtlcss-webpack-plugin' );
const defaultConfig = require( './node_modules/@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,

	context: path.resolve( __dirname, 'assets/src' ),

	entry: {
		main: './main.js',
	},

	// https://webpack.js.org/configuration/output/
	output: {
		...defaultConfig.output,
		uniqueName: '@wearerequired/wp-team-list',
		path: path.resolve( __dirname, 'assets/dist' ),
		filename: '[name].js',
	},

	plugins: [
		...defaultConfig.plugins,
		new RtlCssPlugin( {
			filename: `[name]-rtl.css`,
		} ),
	],
};
