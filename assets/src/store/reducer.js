/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Reducer managing users.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 * @return {Object} Updated state.
 */
export function users( state = { queries: {}, isLoading: {} }, action ) {
	switch ( action.type ) {
		case 'RECEIVE_USERS':
			return {
				...state,
				queries: {
					...state.queries,
					[ action.query ]: action.users,
				},
			};

		case 'RECEIVE_IS_LOADING':
			return {
				...state,
				isLoading: {
					...state.isLoading,
					[ action.query ]: action.isLoading,
				},
			};
	}

	return state;
}

/**
 * Reducer managing roles.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 * @return {Object} Updated state.
 */
export function roles( state = [], action ) {
	switch ( action.type ) {
		case 'RECEIVE_USER_ROLES':
			return action.roles;
	}

	return state;
}

export default combineReducers( {
	users,
	roles,
} );
