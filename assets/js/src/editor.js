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
	RichText,
} from '@wordpress/blocks';
import {
	InspectorControls,
	PanelBody,
	TextControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { withState } from '@wordpress/compose';

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
			default: 10,
		},
		showLink: {
			type: 'boolean',
			default: true,
		},
		showDescription: {
			type: 'boolean',
			default: true,
		},
		roles: {
			type: 'array',
			default: 'administrator'
		},
		orderBy: {
			type: 'string',
			default: 'post_count'
		},
		order: {
			type: 'string',
			default: 'desc'
		}
	},

	edit: withState( {
		editable: '',
	} )( ( { className, attributes: { number, showLink, showDescription, roles, orderBy, order }, setAttributes, isSelected, editable, setState } ) => {
		return (
			<Fragment>
				<TeamList
					number={ number }
					showLink={ showLink }
					showDescription={ showDescription }
					roles={ roles }
					orderBy={ orderBy }
					order={ order }
					className={ className }
				/>
				<InspectorControls>
					<PanelBody>
						<TextControl
							label={ __( 'Number of users to display', 'wp-team-list' ) }
							type="number"
							value={ number }
							onChange={ ( newValue ) => {
								setAttributes( { number: newValue } );
							} }
						/>
						<SelectControl
							label={ __( 'Roles', 'wp-team-list' ) }
							help={ __( 'Only show users with the selected roles', 'wp-team-list' ) }
							value={ roles }
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
							value={ orderBy }
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
							onChange={ ( newValue ) => {
								setAttributes( { orderBy: newValue } );
							} }
						/>
						<SelectControl
							label={ __( 'Order', 'wp-team-list' ) }
							value={ order }
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
							onChange={ ( newValue ) => {
								setAttributes( { order: newValue } );
							} }
						/>
						<ToggleControl
							label={ __( 'Show user description', 'wp-team-list' ) }
							checked={ showDescription }
							onChange={ ( newValue ) => {
								setAttributes( { showDescription: newValue } );
							} }
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	} ),

	save() {
		return null;
	},
} );
