/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { __, _n, sprintf } from "@wordpress/i18n";

export default class TeamMember extends Component {
	render() {
		const {
			user: {
				id,
				role,
				role_display_name,
				display_name,
				avatar_urls,
				description,
				link,
				post_count,
			},
			showDescription,
			className
		} = this.props;

		return (
			<div
				className={ `wp-team-member wp-team-list-item author-${ id } role-${ role } ${ className }` }>

				<figure className="wp-team-member-avatar author-image">
					<img src={ avatar_urls[ 48 ] } srcSet={ `${ avatar_urls[ 96 ] } 2x` } alt="" width="90"/>
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
					  <a
						  href={ link }
						  title={
							  sprintf(
								  /* translators: %s: author display name */
								  __( 'View all posts by %s', 'wp-team-list' ),
								  display_name
							  )
						  }>
						  {
							  sprintf(
								  /* translators: %s: number of posts */
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
