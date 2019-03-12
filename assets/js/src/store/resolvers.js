import {
	receiveUserRoles,
	receiveUsers,
}  from './actions';
import { apiFetch } from './controls';


/**
 * Requests user roles from the REST API.
 */
export function* getUserRoles() {
	const roles = yield apiFetch( { path: '/wp-team-list/v1/roles' } );
	yield receiveUserRoles( roles );
}

/**
 * Requests user roles from the REST API.
 */
export function* getUsers( query ) {
	const users = yield apiFetch( { path: `/wp-team-list/v1/users${ query }` } );
	yield receiveUsers( query, users );
}
