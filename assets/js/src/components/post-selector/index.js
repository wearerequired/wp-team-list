/**
 * WordPress dependencies
 */
import { __, _n, sprintf } from '@wordpress/i18n';
import { BaseControl } from '@wordpress/components';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import Autocomplete from 'accessible-autocomplete/react';
import { debounce } from 'lodash';

/**
 * Internal dependencies
 */
import './accessible-autocomplete.css';

export class PostSelector extends Component {
	constructor() {
		super( ...arguments );

		this.setPost     = this.setPost.bind( this );
		this.suggestPost = this.suggestPost.bind( this );
	}

	setPost( post ) {
		if ( !post ) {
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
		const payload =`?subtype=${ searchablePostTypes.join( ',' ) }&search=${ encodeURIComponent( query ) }`;
		apiFetch( { path: '/wp/v2/search/' + payload } ).then( posts => {
			populateResults( posts );
		} );
	}

	render() {
		const { post, instanceId, label, help, placeholder } = this.props;
		const selectId = 'post-selector-' + instanceId;

		const currentPost = this.post || post;

		return (
			<BaseControl
				id={selectId}
				label={ label }
				help={ help }
			>
				<Autocomplete
					id={selectId}
					minLength={2}
					showAllValues={true}
					defaultValue={currentPost ? currentPost.title.rendered : ''}
					autoselect={true}
					displayMenu="overlay"
					onConfirm={this.setPost}
					source={debounce( this.suggestPost, 300 )}
					showNoResultsFound={true}
					placeholder={ placeholder }
					tStatusQueryTooShort={( minQueryLength ) =>
						sprintf( __( 'Type in %s or more characters for results', 'schilling-content-types' ), minQueryLength )}
					tNoResults={() => __( 'No results found', 'schilling-content-types' )}
					tStatusNoResults={() => __( 'No search results.', 'schilling-content-types' )}
					tStatusSelectedOption={( selectedOption, length ) => sprintf( __( '%1$s (1 of %2$s) is selected', 'schilling-content-types' ), selectedOption, length )}
					tStatusResults={( length, contentSelectedOption ) => {
						return (
							<span>{
								sprintf(
									_n( '%1$s result is available. %2$s', '%s results are available. %2$s', length, 'schilling-content-types' ),
									length,
									contentSelectedOption,
								)
							}</span>
						);
					}}
					templates={
						{
							inputValue: inputValue => {
								if ( inputValue ) {
									return inputValue.title;
								}

								return '';
							},
							suggestion: suggestion => {
								if ( suggestion ) {
									return suggestion.title;
								}

								return ''
							}
						}
					}
				/>
			</BaseControl>
		);
	}
}

export default PostSelector;
