/**
 * WordPress dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { BaseControl } from '@wordpress/components';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { withInstanceId } from '@wordpress/compose';

/**
 * External dependencies
 */
import Autocomplete from 'accessible-autocomplete/react';
import { debounce } from 'lodash';

/**
 * Internal dependencies
 */
import './accessible-autocomplete.css';
import './style.css';

class PostSelector extends Component {
	constructor() {
		super( ...arguments );

		this.setPost = this.setPost.bind( this );
		this.suggestPost = this.suggestPost.bind( this );
	}

	setPost( post ) {
		if ( ! post ) {
			return;
		}

		this.post = post;

		const { onUpdatePost } = this.props;

		onUpdatePost( post );
	}

	suggestPost( query, populateResults ) {
		if ( query.length < 2 ) {
			return;
		}

		const searchablePostTypes = this.props.searchablePostTypes || [ 'post' ];
		const payload = `?subtype=${ searchablePostTypes.join( ',' ) }&search=${ encodeURIComponent( query ) }`;
		apiFetch( { path: '/wp/v2/search/' + payload } ).then( ( posts ) => {
			populateResults( posts );
		} );
	}

	render() {
		const { post, instanceId, label, help, placeholder } = this.props;
		const selectId = 'post-selector-' + instanceId;

		const defaultValue = post ? post.title.rendered : '';

		return (
			<BaseControl
				id={ selectId }
				label={ label }
				help={ help }
			>
				<Autocomplete
					id={ selectId }
					minLength={ 2 }
					showAllValues={ true }
					defaultValue={ defaultValue }
					autoselect={ true }
					displayMenu="overlay"
					onConfirm={ this.setPost }
					source={ debounce( this.suggestPost, 300 ) }
					showNoResultsFound={ true }
					placeholder={ placeholder }
					tStatusQueryTooShort={ ( minQueryLength ) =>
						sprintf(
							/* translators: %s: minimum character length */
							__( 'Type in %s or more characters for results', 'wp-team-list' ),
							minQueryLength
						)
					}
					tNoResults={ () => __( 'No results found', 'wp-team-list' ) }
					tStatusNoResults={ () => __( 'No search results.', 'wp-team-list' ) }
					tStatusSelectedOption={ ( selectedOption, length, index ) =>
						sprintf(
							/* translators: 1: selected option, 2: index of selected option, 3: count of available options */
							__( '%1$s (%2$s of %3$s) is selected', 'wp-team-list' ),
							selectedOption,
							index + 1,
							length
						)
					}
					tStatusResults={ ( length, contentSelectedOption ) => {
						return (
							<span>{
								sprintf(
									/* translators: 1: count of available options, 2: selected option */
									_n( '%1$s result is available. %2$s', '%1$s results are available. %2$s', length, 'wp-team-list' ),
									length,
									contentSelectedOption,
								)
							}</span>
						);
					} }
					templates={
						{
							inputValue: ( inputValue ) => {
								if ( inputValue ) {
									return inputValue.title;
								}

								return '';
							},
							suggestion: ( suggestion ) => {
								if ( suggestion ) {
									return suggestion.title;
								}

								return '';
							},
						}
					}
				/>
			</BaseControl>
		);
	}
}

export default withInstanceId( PostSelector );
