/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';

export const name = 'required/wp-team-list';

export const settings = {
	title: __( 'Team List', 'wp-team-list' ),

	description: __( 'Display website authors.', 'wp-team-list' ),

	category: 'widgets',

	icon: 'groups',

	keywords: [
		__( 'team', 'wp-team-list' ),
		__( 'list', 'wp-team-list' ),
		__( 'authors', 'wp-team-list' ),
	],

	supports: {
		anchor: false,
		customClassName: false,
		html: false,
	},

	attributes: {
		number: {
			type: 'number',
			default: 10,
		},
		postId: {
			type: 'number',
		},
		postType: {
			type: 'string',
		},
		showDescription: {
			type: 'boolean',
			default: true,
		},
		roles: {
			type: 'array',
			default: [],
		},
		orderBy: {
			type: 'string',
			default: 'post_count',
		},
		order: {
			type: 'string',
			default: 'desc',
		},
	},

	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'core/legacy-widget' ],
				isMatch: ( { idBase, instance } ) => {
					if ( ! instance?.raw ) {
						// Can't transform if raw instance is not shown in REST API.
						return false;
					}
					return idBase === 'wp-team-list';
				},
				transform: ( { instance } ) => {
					const blocks = [];

					if ( instance.raw.title ) {
						blocks.push(
							createBlock( 'core/heading', {
								content: instance.raw.title,
							} )
						);
					}

					blocks.push(
						createBlock( 'required/wp-team-list', {
							number: instance.raw.number,
							roles: [ instance.raw.role ],
						} )
					);

					if ( instance.raw.show_link && instance.raw.page_url ) {
						blocks.push(
							createBlock( 'core/paragraph', {
								content:
									'<a href="' +
									instance.raw.page_url +
									'">' +
									__(
										'Show all team members',
										'wp-team-list'
									) +
									'</a>',
							} )
						);
					}
					return blocks;
				},
			},
		],
	},

	edit,

	save() {
		return null;
	},
};
