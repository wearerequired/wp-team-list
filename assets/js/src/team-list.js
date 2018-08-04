/**
 * WordPress dependencies
 */
import {
	Component,
	Fragment,
	Spinner,
	SelectControl,
	Placeholder,
} from '@wordpress/element';
import {
	withSelect,
	withDispatch,
	registerStore,
	fetch,
	apiFetch,
	dispatch
} from '@wordpress/data';
import {
	sprintf,
	__,
	_n,
} from '@wordpress/i18n';

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
			dispatch( 'wp-team-list' ).setLoading( false );
			dispatch( 'wp-team-list' ).setUsers( query, users );
		},
	},
} );

class TeamMember extends Component {
	render() {
		const { user: { id: userId, role, name: displayName, avatar, avatar2x, description, archiveUrl, postCount }, showDescription, className } = this.props;

		return (
			<div
				className={`wp-team-member wp-team-list-item author-${userId} role-${role} ${className}`}>

				<figure className="wp-team-member-avatar author-image">
					<img src={avatar} srcSet={`${avatar2x} 2x`} alt="" width="90" />
				</figure>

				<h2 className="wp-team-member-name">{ displayName }</h2>

				{ role &&
					<p className="wp-team-member-role">{ role }</p>
				}

				{ showDescription &&
					<p className="wp-team-member-description">{ description }</p>
				}

				{ postCount &&
					<p className="wp-team-member-posts-link">
						<a href={ archiveUrl } title={ sprintf( __( 'View all posts by %s', 'wp-team-list' ), displayName ) }>
							{
								sprintf(
									_n( 'View %d post', 'View %d posts', postCount, 'wp-team-list' ),
									postCount
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

		if ( isLoading ) {
			return <Spinner />
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
			return <TeamMember user={ user } showDescription={ showDescription } />
		} );

		return (
			<div>
				{ teamList }
				{ showLink &&
					<a href="" className="show-all">{ __( 'Show all team members', 'wp-team-list' ) }</a>
				}
			</div>
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
