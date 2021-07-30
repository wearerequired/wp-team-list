/**
 * WordPress dependencies
 */
import { Fragment, Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { withSelect } from '@wordpress/data';
import {
	Button,
	PanelBody,
	SelectControl,
	ToggleControl,
	RangeControl,
} from '@wordpress/components';
const { InspectorControls } = wp.blockEditor || wp.editor;

/**
 * Internal dependencies
 */
import { TeamList, PostSelector, MultiSelectControl } from '../../components';

class TeamListEdit extends Component {
	render() {
		const {
			className,
			post,
			availableRoles,
			attributes: { number, showDescription, roles, orderBy, order },
			setAttributes,
		} = this.props;

		return (
			<Fragment>
				<TeamList
					number={ number }
					linkTo={ post && post.link }
					showDescription={ showDescription }
					roles={ roles }
					orderBy={ orderBy }
					order={ order }
					className={ className }
				/>
				<InspectorControls>
					<PanelBody>
						<RangeControl
							label={ __(
								'Number of users to display',
								'wp-team-list'
							) }
							value={ number }
							onChange={ ( value ) => setAttributes( { value } ) }
							min={ 1 }
							max={ 100 }
						/>
						<MultiSelectControl
							label={ __( 'Roles', 'wp-team-list' ) }
							help={ __(
								'Only show users with the selected roles',
								'wp-team-list'
							) }
							placeholder={ __(
								'Select or leave empty for all',
								'wp-team-list'
							) }
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
							label={ __(
								'Show user description',
								'wp-team-list'
							) }
							checked={ showDescription }
							onChange={ ( newValue ) => {
								setAttributes( { showDescription: newValue } );
							} }
						/>
						<PostSelector
							label={ __( 'Link to', 'wp-team-list' ) }
							help={ __(
								'Select a team page to link to.',
								'wp-team-list'
							) }
							searchablePostTypes={ [ 'page' ] }
							post={ post }
							onUpdatePost={ ( {
								id: postId,
								subtype: postType,
							} ) => {
								setAttributes( { postId, postType } );
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
									isLink
									isDestructive
								>
									{ __( 'Clear selection', 'wp-team-list' ) }
								</Button>
							</p>
						) }
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	}
}

export default withSelect( ( select, ownProps ) => {
	const {
		attributes: { postId, postType },
	} = ownProps;
	const { getUserRoles } = select( 'wp-team-list' );

	return {
		post:
			postType && postId
				? select( 'core' ).getEntityRecord(
						'postType',
						postType,
						postId
				  )
				: null,
		availableRoles: getUserRoles(),
	};
} )( TeamListEdit );
