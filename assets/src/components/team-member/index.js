/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { _n, sprintf } from '@wordpress/i18n';

export default class TeamMember extends Component {
	render() {
		const {
			user: {
				id,
				role,
				role_display_name: roleDisplayName,
				display_name: displayName,
				avatar_urls: avatarUrls,
				description,
				link,
				post_count: postCount,
			},
			showDescription,
		} = this.props;

		return (
			<div className={ `wp-team-member wp-team-list-item author-${ id } role-${ role }` }>
				<figure className="wp-team-member-avatar author-image">
					<img
						src={ avatarUrls[ 90 ] }
						srcSet={ `${ avatarUrls[ 180 ] } 2x` }
						alt=""
						className="avatar avatar-90 photo"
						height="90"
						width="90"
					/>
				</figure>

				<h2 className="wp-team-member-name">{ displayName }</h2>

				{ roleDisplayName && <p className="wp-team-member-role">{ roleDisplayName }</p> }

				{ showDescription && <p className="wp-team-member-description">{ description }</p> }

				{ postCount > 0 && (
					<p className="wp-team-member-posts-link">
						<a href={ link }>
							{ sprintf(
								/* translators: %s: number of posts */
								_n( 'View %s post', 'View %s posts', postCount, 'wp-team-list' ),
								postCount
							) }
						</a>
					</p>
				) }
			</div>
		);
	}
}
