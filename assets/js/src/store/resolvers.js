/**
 * Internal dependencies
 */
import {
	dispatch,
	apiFetch,
} from './controls';
import {
	receiveUserRoles,
	receiveUsers,
} from './actions';
import { STORE_KEY } from './name';

/**
 * Requests user roles from the REST API.
 */
export function* getUserRoles() {
	const roles = yield apiFetch( { path: '/wp-team-list/v1/roles' } );
	yield receiveUserRoles( roles );
}

/**
 * Requests user roles from the REST API.
 *
 * @param {string} query
 */
export function* getUsers( query ) {
	yield dispatch(
		STORE_KEY,
		'setLoading',
		query,
		true,
	);

	const users = yield apiFetch( { path: `/wp-team-list/v1/users${ query }` } );
	yield receiveUsers( query, users );

	yield dispatch(
		STORE_KEY,
		'setLoading',
		query,
		false,
	);
}
