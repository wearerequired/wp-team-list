/**
 * WordPress dependencies
 */

import { registerBlockType } from '@wordpress/blocks';
import { registerStore } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { TeamListName, TeamListSettings } from './blocks';
import { name as usersStoreName, settings as usersStoreSettings } from './stores/users';

registerStore( usersStoreName, usersStoreSettings );

registerBlockType( TeamListName, TeamListSettings );
