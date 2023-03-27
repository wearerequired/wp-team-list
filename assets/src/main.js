/**
 * WordPress dependencies
 */

import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import './store';
import './style.css';
import { TeamListName, TeamListSettings } from './blocks';

registerBlockType( TeamListName, TeamListSettings );
