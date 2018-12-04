/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = {
	users: {},
	isLoading: {},
};

export const name = 'wp-team-list/users';

export const settings = {
	reducer( state = DEFAULT_STATE, action ) {
		switch ( action.type ) {
			case 'SET_USERS':
				return {
					...state,
					users: {
						...state.users,
						[ action.query ]: action.users,
					},
				};

			case 'SET_IS_LOADING':
				return {
					...state,
					isLoading: {
						...state.isLoading,
						[ action.query ]: action.isLoading,
					},
				};
		}

		return state;
	},

	actions: {
		setUsers( query, users ) {
			return {
				type: 'SET_USERS',
				query,
				users,
			};
		},

		setLoading( query, isLoading ) {
			return {
				type: 'SET_IS_LOADING',
				query,
				isLoading,
			}
		}
	},

	selectors: {
		getUsers( state, query ) {
			const { users } = state;

			return users[ query ];
		},

		isLoading( state, query ) {
			const { isLoading } = state;

			return isLoading[ query ];
		},
	},

	resolvers: {
		async getUsers( query ) {
			dispatch( 'wp-team-list/users' ).setLoading( query, true );

			try {
				const users = await apiFetch( { path: '/wp-team-list/v1/users?' + query } );
				dispatch( 'wp-team-list/users' ).setUsers( query, users );
			} catch( e ) {
				console.log( `[WP Team List] Error ${e.code} while trying to load users: ${e.message}` );
			} finally {
				dispatch( 'wp-team-list/users' ).setLoading( query, false );
			}
		},
	},
};
