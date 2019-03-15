module.exports = ( ctx ) => {
	const config = {
		plugins: {
			'postcss-import': {} ,
			'postcss-mixins': {} ,
			'postcss-nested': {} ,
			'postcss-preset-env': {
				stage: 0,
				preserve: false, // Omit pre-polyfilled CSS.
				features: {
					'nesting-rules': false, /* Uses postcss-nesting which doesn't behave like Sass. */
				},
				autoprefixer: {
					grid: true,
				},
			} ,
			'postcss-hexrgba': {} ,
			'css-mqpacker': {
				sort: true,
			} ,
		}
	};

	if ( 'development' === ctx.env ) {
		config.map = true;
	} else {
		config.map = false;
		config.plugins['cssnano'] = {
			safe: true,
		}
	}

	return config;
};
