/**
 * WordPress dependencies
 */
import { default as triggerApiFetch } from '@wordpress/api-fetch';
import {
	select as selectData,
	dispatch as dispatchData,
} from '@wordpress/data';

/**
 * Trigger an API Fetch request.
 *
 * @param {Object} request API Fetch Request Object.
 * @return {Object} control descriptor.
 */
export function apiFetch( request ) {
	return {
		type: 'API_FETCH',
		request,
	};
}

/**
 * Dispatches a control action for triggering a registry select.
 *
 * @param {string} storeKey
 * @param {string} selectorName
 * @param {Array}  args         Arguments for the select.
 * @return {Object} control descriptor.
 */
export function select( storeKey, selectorName, ...args ) {
	return {
		type: 'SELECT',
		storeKey,
		selectorName,
		args,
	};
}

/**
 * Dispatches a control action for triggering a registry dispatch.
 *
 * @param {string} storeKey
 * @param {string} actionName
 * @param {Array}  args       Arguments for the dispatch action.
 * @return {Object}  control descriptor.
 */
export function dispatch( storeKey, actionName, ...args ) {
	return {
		type: 'DISPATCH',
		storeKey,
		actionName,
		args,
	};
}

const controls = {
	API_FETCH( { request } ) {
		return triggerApiFetch( request );
	},

	SELECT( { selectorName, args } ) {
		return selectData( 'wp-team-list' )[ selectorName ]( ...args );
	},

	DISPATCH( { actionName, args } ) {
		return dispatchData( 'wp-team-list' )[ actionName ]( ...args );
	},
};

export default controls;
