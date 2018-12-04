const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );

const externals = {
	react: 'React',
	'react-dom': 'ReactDOM',
	jquery: 'jQuery',
	lodash: 'lodash',
};

// Define WordPress dependencies
const wpDependencies = [
	'api-fetch',
	'blocks',
	'components',
	'compose',
	'date',
	'editor',
	'element',
	'hooks',
	'i18n',
	'utils',
	'data',
	'viewport',
	'core-data',
	'plugins',
	'edit-post',
];

/**
 * Given a string, returns a new string with dash separators converted to
 * camel-case equivalent. This is not as aggressive as `_.camelCase` in
 * converting to uppercase, where Lodash will convert letters following
 * numbers.
 *
 * @param {string} string Input dash-delimited string.
 *
 * @return {string} Camel-cased string.
 */
function camelCaseDash( string ) {
	return string.replace(
		/-([a-z])/,
		( match, letter ) => letter.toUpperCase()
	);
}

wpDependencies.forEach( ( name ) => {
	externals[ `@wordpress/${ name }` ] = {
		this: [ 'wp', camelCaseDash( name ) ],
	};
} );

const isProduction = process.env.NODE_ENV === 'production';

const config = {
	mode: isProduction ? 'production' : 'development',

	devtool: isProduction ? undefined : 'inline-source-map',

	// https://webpack.js.org/configuration/entry-context/#context
	context: path.resolve( __dirname, 'assets/js/src' ),

	// https://webpack.js.org/configuration/entry-context/
	entry: {
		'editor': './editor.js',
	},

	// https://webpack.js.org/configuration/output/
	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: '[name].js',
		library: 'TeamList',
		libraryTarget: 'this',
	},

	// https://webpack.js.org/configuration/externals/
	externals,

	optimization: {
		runtimeChunk: false,
		minimizer: [
			new UglifyJsPlugin( {
				cache: true,
				parallel: true,
				uglifyOptions: {
					output: {
						comments: false,
					}
				}
			} )
		]
	},

	// https://github.com/babel/babel-loader#usage
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: 'babel-loader',
			},
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader'
				]
			}
		],
	},

	// https://webpack.js.org/configuration/plugins/
	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../css/[name].css',
		} ),
	],
};

module.exports = config;
