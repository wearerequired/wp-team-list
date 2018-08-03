const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const externals = {
	jquery: 'jQuery',
	lodash: 'lodash',
};

// Define WordPress dependencies
const wpDependencies = [
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

// CSS loader for styles specific to block editing.
const editBlocksCSSPlugin = new ExtractTextPlugin( {
	filename: '../css/edit-blocks.css',
} );

const postCssPlugins = process.env.NODE_ENV === 'production' ?
	[
		require( 'postcss-nested' ),
		require( 'autoprefixer' ),
		require( 'cssnano' )( {
			safe: true,
		} )
	] :
	[
		require( 'postcss-nested' ),
		require( 'autoprefixer' ),
	];

// Configuration for the ExtractTextPlugin.
const extractConfig = {
	use: [
		{ loader: 'raw-loader' },
		{
			loader: 'postcss-loader',
			options: {
				plugins: postCssPlugins,
			},
		},
	],
};

const config = {
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',

	// https://webpack.js.org/configuration/entry-context/
	entry: {
		'editor': './assets/js/src/editor.js',
	},

	// https://webpack.js.org/configuration/output/
	output: {
		path: __dirname + '/assets/js/',
		filename: '[name].js',
		library: 'WPTEAMLIST',
		libraryTarget: 'this',
	},

	// https://webpack.js.org/configuration/externals/
	externals,

	// https://github.com/babel/babel-loader#usage
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: 'babel-loader',
			},
			{
				test: /editor\.css$/,
				use: editBlocksCSSPlugin.extract( extractConfig ),
			},
		],
	},

	// https://webpack.js.org/configuration/plugins/
	plugins: [
		editBlocksCSSPlugin,
	]
};

module.exports = config;
