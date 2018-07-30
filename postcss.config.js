module.exports = ( ctx ) => ({
	plugins: [
		require( 'postcss-import' )( {} ),
		require( 'postcss-mixins' )( {} ),
		require( 'postcss-utilities' )( {} ),
		require( 'postcss-nested' )( {} ),
		require( 'postcss-custom-properties' )( {
			preserve: false,
		} ),
		require( 'postcss-hexrgba' )( {} ),
		require( 'css-mqpacker' )( {} ),
		require( 'autoprefixer' )( {
			browsers: [
				'last 2 versions',
			]
		} ),
		require( 'cssnano' )( {
			safe: true,
		} ),
	]
});
