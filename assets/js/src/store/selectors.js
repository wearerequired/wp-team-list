export function getUsers( state, query ) {
	return state.users.queries[ query ];
}

export function isLoading( state, query ) {
 	return state.users.isLoading[ query ];
}

export function getUserRoles( state ) {
	return state.roles;
}
