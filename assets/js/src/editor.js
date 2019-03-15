/**
 * WordPress dependencies
 */

import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import './store';
import { TeamListName, TeamListSettings } from './blocks';

registerBlockType( TeamListName, TeamListSettings );
