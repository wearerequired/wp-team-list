/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	Button,
	PanelBody,
	SelectControl,
	ToggleControl,
	RangeControl,
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { TeamList, PostSelector, MultiSelectControl } from '../../components';

const TeamListEdit = ( { className, attributes, setAttributes } ) => {
	const { number, showDescription, roles, orderBy, order, postId, postType } = attributes;
	const availableRoles = useSelect( ( select ) => {
		return select( 'wp-team-list' ).getUserRoles();
	}, [] );
	const post = useSelect(
		( select ) => {
			return select( 'core' ).getEntityRecord( 'postType', postType, postId, {
				context: 'view',
			} );
		},
		[ postId, postType ]
	);

	return (
		<>
			<div { ...useBlockProps() }>
				<TeamList
					number={ number }
					linkTo={ post?.link }
					showDescription={ showDescription }
					roles={ roles }
					orderBy={ orderBy }
					order={ order }
					className={ className }
				/>
			</div>
			<InspectorControls>
				<PanelBody>
					<RangeControl
						label={ __( 'Number of users to display', 'wp-team-list' ) }
						value={ number }
						onChange={ ( value ) => setAttributes( { value } ) }
						min={ 1 }
						max={ 100 }
					/>
					<MultiSelectControl
						label={ __( 'Roles', 'wp-team-list' ) }
						help={ __( 'Only show users with the selected roles', 'wp-team-list' ) }
						placeholder={ __( 'Select or leave empty for all', 'wp-team-list' ) }
						value={ roles }
						options={ availableRoles }
						onChange={ ( newValue ) => {
							setAttributes( { roles: newValue } );
						} }
						isClearable={ false }
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
							},
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
					<PostSelector
						label={ __( 'Link to', 'wp-team-list' ) }
						help={ __( 'Select a team page to link to.', 'wp-team-list' ) }
						searchablePostTypes={ [ 'page' ] }
						post={ post }
						onUpdatePost={ ( { id, subtype } ) => {
							setAttributes( { postId: id, postType: subtype } );
						} }
					/>
					{ post && (
						<p>
							<Button
								onClick={ () => {
									setAttributes( {
										postId: null,
										postType: null,
									} );
								} }
								variant="link"
								isDestructive
							>
								{ __( 'Clear selection', 'wp-team-list' ) }
							</Button>
						</p>
					) }
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default TeamListEdit;
