/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import controls from './controls';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { REDUCER_KEY } from './name';

registerStore( REDUCER_KEY, {
	reducer,
	controls,
	actions,
	selectors,
	resolvers,
} );
