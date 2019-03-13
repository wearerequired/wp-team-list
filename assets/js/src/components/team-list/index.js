/**
 * WordPress dependencies
 */
import {
	Component,
	Fragment,
} from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import './editor.css';
import { TeamMember } from '../';

class TeamList extends Component {
	render() {
		const { users, linkTo, showDescription, isLoading } = this.props;

		if ( isLoading ) {
			return <Spinner/>;
		}

		if ( ! users || ! users.length ) {
			return (
				<p>
					{ __( 'There are no users to show.', 'wp-team-list' ) }
				</p>
			)
		}

		const teamList = users.map( user => {
			return <TeamMember user={ user } showDescription={ showDescription } key={ user.id }/>
		} );

		return (
			<Fragment>
				{ teamList }
				{ linkTo &&
				  <a href={ linkTo } className="show-all">{ __( 'Show all team members', 'wp-team-list' ) }</a>
				}
			</Fragment>
		)
	}
}

export default withSelect( ( select, ownProps ) => {
	const { number, role, orderBy, order, showLink, showDescription } = ownProps;
	const { getUsers, isLoading } = select( 'wp-team-list' );

	const queryArgs = {
		order,
		order_by: orderBy,
		role,
		per_page: number,
	};

	const queryString = addQueryArgs( '', queryArgs );

	return {
		users: getUsers( queryString ),
		isLoading: isLoading( queryString ),
		showLink,
		showDescription,
	};
} )( TeamList );
