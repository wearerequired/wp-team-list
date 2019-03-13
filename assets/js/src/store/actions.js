/**
 * External dependencies
 */
import { castArray } from 'lodash';

export function receiveUsers( query, users ) {
	return {
		type: 'RECEIVE_USERS',
		query,
		users,
	};
}

export function setLoading( query, isLoading ) {
	return {
		type: 'RECEIVE_IS_LOADING',
		query,
		isLoading,
	};
}

export function receiveUserRoles( roles ) {
	return {
		type: 'RECEIVE_USER_ROLES',
		roles: castArray( roles ),
	};
}
