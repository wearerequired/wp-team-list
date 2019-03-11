/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = {
	posts: {},
};

export const name = 'wp-team-list/posts';

export const settings = {
	reducer( state = DEFAULT_STATE, action ) {
		switch ( action.type ) {
			case 'SET_POST':
				return {
					...state,
					posts: {
						...state.posts,
						[ action.postId ]: action.post,
					},
				};
		}

		return state;
	},

	actions: {
		setPost( postId, post ) {
			return {
				type: 'SET_POST',
				postId,
				post,
			};
		},
	},

	selectors: {
		getPost( state, postId ) {
			const { posts } = state;

			return posts[ postId ] || null;
		},
	},

	resolvers: {
		async getPost( postId, postType ) {
			// TODO: postType should be rest_base of the post type object.
			const post = postId ? await apiFetch( { path: `/wp/v2/${ postType }s/${ postId }?_embed` } ) : null;
			dispatch( 'wp-team-list/posts' ).setPost( postId, post );
		},
	},
};
