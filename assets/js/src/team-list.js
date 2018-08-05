/**
 * WordPress dependencies
 */
import {
	Component,
	Fragment,
	SelectControl,
	Placeholder,
} from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import {
	withSelect,
	withDispatch,
	registerStore,
	fetch,
	dispatch
} from '@wordpress/data';
import {
	sprintf,
	__,
	_n,
} from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = {
	users: {},
	isLoading: {},
};

registerStore( 'wp-team-list', {
	reducer( state = DEFAULT_STATE, action ) {
		switch ( action.type ) {
			case 'SET_USERS':
				return {
					...state,
					users: {
						...state.users,
						[ action.query ]: action.users,
					},
				};

			case 'SET_IS_LOADING':
				return {
					...state,
					isLoading: {
						...state.isLoading,
						[ action.query ]: action.isLoading,
					},
				};
		}

		return state;
	},

	actions: {
		setUsers( query, users ) {
			return {
				type: 'SET_USERS',
				query,
				users,
			};
		},

		setLoading( query, isLoading ) {
			return {
				type: 'SET_IS_LOADING',
				query,
				isLoading,
			}
		}
	},

	selectors: {
		getUsers( state, query ) {
			const { users } = state;

			return users[ query ];
		},

		isLoading( state, query ) {
			const { isLoading } = state;

			return isLoading[ query ];
		},
	},

	resolvers: {
		async getUsers( state, query ) {
			dispatch( 'wp-team-list' ).setLoading( query, true );
			const users = await apiFetch( { path: '/wp-team-list/v1/users?'+ query } );
			dispatch( 'wp-team-list' ).setLoading( query, false );
			dispatch( 'wp-team-list' ).setUsers( query, users );
		},
	},
} );

class TeamMember extends Component {
	render() {
		const { user: { id, role, role_display_name, display_name, avatar_urls, description, link, post_count }, showDescription, className } = this.props;

		return (
			<div
				className={`wp-team-member wp-team-list-item author-${id} role-${role} ${className}`}>

				<figure className="wp-team-member-avatar author-image">
					<img src={ avatar_urls[48] } srcSet={`${avatar_urls[96]} 2x`} alt="" width="90" />
				</figure>

				<h2 className="wp-team-member-name">{ display_name }</h2>

				{ role_display_name &&
					<p className="wp-team-member-role">{ role_display_name }</p>
				}

				{ showDescription &&
					<p className="wp-team-member-description">{ description }</p>
				}

				{ post_count &&
					<p className="wp-team-member-posts-link">
						<a href={ link } title={ sprintf( __( 'View all posts by %s', 'wp-team-list' ), display_name ) }>
							{
								sprintf(
									_n( 'View %s post', 'View %s posts', post_count, 'wp-team-list' ),
									post_count
								)
							}
						</a>
					</p>
				}
			</div>
		)
	}
}

class TeamList extends Component {
	render() {
		const { users, showLink, showDescription, isLoading } = this.props;

		if ( isLoading && ! users ) {
			return <Spinner />;
		}

		if ( ! users ) {
			return (
				<p>
					{
						__(
							'There are no users to show.',
							'wp-team-list'
						)
					}
				</p>
			)
		}

		const teamList = users.map( user => {
			return <TeamMember user={ user } showDescription={ showDescription } key={ user.id } />
		} );

		return (
			<Fragment>
				{ isLoading && <Spinner /> }
				{ teamList }
				{ showLink &&
					<a href="" className="show-all">{ __( 'Show all team members', 'wp-team-list' ) }</a>
				}
			</Fragment>
		)
	}
}

export default withSelect( ( select, ownProps ) => {
	const { number, roles, orderBy, order, showLink, showDescription } = ownProps;
	const { getUsers, isLoading } = select( 'wp-team-list' );
	const queryArgs = {
		order,
		order_by: orderBy,
		roles,
		per_page: number,
	};

	const queryString = Object.keys( queryArgs ).map( arg => {
		return encodeURIComponent( arg ) + '=' + encodeURIComponent( queryArgs[ arg ] );
	} ).reduce( ( result, currentArg ) => {
		return `${result}&${currentArg}`;
	} );

	return {
		users: getUsers( queryString ),
		isLoading: isLoading( queryString ),
		showLink,
		showDescription,
	};
} )( TeamList );
