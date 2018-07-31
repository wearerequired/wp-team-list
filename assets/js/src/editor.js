/**
 * WordPress dependencies
 */
import {
	Component,
	Fragment,
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	registerBlockType,
	RichText
} from '@wordpress/blocks';
import {
	TextControl,
	withState
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import './editor.css';
import TeamList from './team-list';

registerBlockType( 'required/wp-team-list', {
	title:  __( 'Team List', 'wp-team-list' ),

	description: __( 'Display website authors.', 'wp-team-list' ),

	category: 'widgets',

	icon: 'groups',

	keywords: [
		__( 'team', 'wp-team-list' ),
		__( 'list', 'wp-team-list' ),
		__( 'authors', 'wp-team-list' ),
	],

	supports: {
		anchor: false,
		customClassName: false,
		html: false,
	},

	attributes: {
		number: {
			type: 'number',
		},
		showLink: {
			type: 'boolean',
		},
		showDescription: {
			type: 'boolean',
		},
		roles: {
			type: 'array',
		},
		orderBy: {
			type: 'string',
		},
		order: {
			type: 'string',
		}
	},

	edit: withState( {
		editable: '',
	} )( ( { className, attributes, setAttributes, isSelected, editable, setState } ) => {
		return (
			<Fragment>
				<TextControl
					label={ __( 'Number of users to display', 'wp-team-list' ) }
					type="number"
					value={ attributes.number }
					onChange={ ( newValue ) => {
						setAttributes( { number: newValue } );
					} }
				/>
				<SelectControl
					label={ __( 'Roles', 'wp-team-list' ) }
					help={ __( 'Only show users with the selected roles', 'wp-team-list' ) }
					value={ attributes.roles }
					options={ [
						{
							value: 'administrator',
							label: 'Administrator',
						},
						{
							value: 'subscriber',
							label: 'Subscriber',
						},
						{
							value: 'editor',
							label: 'Editor',
						}
					] }
					multiple={true}
					onChange={ ( newValue ) => {
						setAttributes( { roles: newValue } );
					} }
				/>
				<SelectControl
					label={ __( 'Order By', 'wp-team-list' ) }
					value={ null }
					options={ [
						{
							value: 'post_count',
							label: __( 'Post Count', 'wp-team-list' ),
						},
						{
							value: 'first_name',
							label: __( 'First Name', 'wp-team-list' ),
						},
						{
							value: 'last_name',
							label: __( 'Last Name', 'wp-team-list' ),
						},
					] }
					multiple={true}
					onChange={ ( newValue ) => {
						setAttributes( { orderBy: newValue } );
					} }
				/>
				<SelectControl
					label={ __( 'Order', 'wp-team-list' ) }
					value={ null }
					options={ [
						{
							value: 'asc',
							label: __( 'Ascending', 'wp-team-list' ),
						},
						{
							value: 'desc',
							label: __( 'Descending', 'wp-team-list' ),
						}
					] }
					multiple={true}
					onChange={ ( newValue ) => {
						setAttributes( { order: newValue } );
					} }
				/>
				<CheckboxControl
					label={ __( 'Show user description', 'wp-team-list' ) }
					checked={ attributes.showDescription }
					onChange={ ( newValue ) => {
						setAttributes( { showDescription: newValue } );
					} }
				/>
			</Fragment>
		);
	} ),

	save( { attributes } ) {
		const { number, showLink, showDescription, roles, orderBy, order } = attributes;

		return (
			<Fragment>
				<TeamList
					number={ number }
					showLink={ showLink }
					showDescription={ showDescription }
					roles={ roles }
					orderBy={ orderBy }
					order={ order }
				/>
			</Fragment>
		);
	},
} );
