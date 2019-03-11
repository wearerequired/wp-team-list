/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

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
			default: [ 'administrator' ]
		},
		orderBy: {
			type: 'string',
			default: 'post_count'
		},
		order: {
			type: 'string',
			default: 'desc'
		}
	},

	edit,

	save() {
		return null;
	},
};
