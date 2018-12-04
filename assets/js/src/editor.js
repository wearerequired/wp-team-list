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
import { name as postsStoreName, settings as postsStoreSettings } from './stores/posts';

registerStore( usersStoreName, usersStoreSettings );
registerStore( postsStoreName, postsStoreSettings );

registerBlockType( TeamListName, TeamListSettings );
