/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';
import { attrs } from '@wordpress/shortcode';

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
		include: {
			type: 'array',
			default: [],
		},
		hasPublishedPosts: {
			type: 'array',
			default: [],
		},
	},

	transforms: {
		from: [
			{
				type: 'block',
				blocks: ['core/shortcode'],
				isMatch: ({ text }) => {
					return text && text.startsWith('[wp_team_list');
				},
				transform: ({ text }) => {
					const shortcode = attrs( text );
					const attributes = {
						roles: shortcode.named.role ? shortcode.named.role.split(',') : [],
						orderBy: shortcode.named.orderby,
						order: shortcode.named.order ? shortcode.named.order.toLowerCase : 'desc',
						include: shortcode.named.include ? shortcode.named.include.split(',') : [],
						hasPublishedPosts: shortcode.named.has_published_posts ? shortcode.named.has_published_posts.split(',') : [],
					}
					return createBlock(name, attributes);
				},
			},
			{
				type: 'shortcode',
				tag: 'wp_team_list',
				attributes: {
					roles: {
						type: 'array',
						shortcode: ( { named: { role } } ) => {
							return role ? role.split(',') : [];
						},
					},
					orderBy: {
						type: 'string',
						shortcode: ( { named: { orderby } } ) => {
							return orderby;
						},
					},
					order: {
						type: 'string',
						shortcode: ( { named: { order } } ) => {
							return order.toLowerCase();
						},
					},
					include: {
						type: 'array',
						shortcode: ( { named: { include } } ) => {
							return include ? include.split(',') : [];
						},
					},
					hasPublishedPosts: {
						type: 'array',
						shortcode: ( { named: { has_published_posts } } ) => {
							return has_published_posts ? has_published_posts.split(',') : [];
						},
					},
				},
			},
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

					blocks.push(
						createBlock( 'core/heading', {
							content:
								instance.raw.title ||
								__( 'Editors', 'wp-team-list' ),
						} )
					);

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
