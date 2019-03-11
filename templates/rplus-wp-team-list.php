<?php
/**
 * WP Team List template file.
 *
 * To override this template, simply copy this file in your
 * theme folder. Example:
 *
 * /wp-content/themes/<your theme>/rplus-wp-team-list.php
 *
 * @package WP_Team_List
 */

// phpcs:disable WordPress.NamingConventions, VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable, WordPress.WP.GlobalVariablesOverride.Prohibited -- Variables are not global.

$role_display_name = wp_team_list()->get_user_role( $user, 'display_name' );
$role              = wp_team_list()->get_user_role( $user, 'name' );
$role_class        = sanitize_html_class( 'role-' . $role );
$description       = get_user_meta( $user->ID, 'description', true );

/* translators: %s: author name */
$title_attr = sprintf( esc_attr__( 'View all posts by %s', 'wp-team-list' ), $user->data->display_name );
?>
<div class="wp-team-member wp-team-list-item author-<?php echo esc_attr( $user->ID ); ?> <?php echo esc_attr( $role_class ); ?>">
	<figure class="wp-team-member-avatar author-image">
		<?php echo wp_team_list()->get_avatar( $user ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
	</figure>

	<h2 class="wp-team-member-name"><?php echo esc_html( $user->data->display_name ); ?></h2>

	<?php
	if ( '' !== $role_display_name ) {
		printf( '<p class="wp-team-member-role">%s</p>', esc_html( $role_display_name ) );
	}

	if ( '' !== $description ) {
		printf( '<p class="wp-team-member-description">%s</p>', wp_kses_post( $description ) );
	}
	?>

	<?php if ( 0 < count_user_posts( $user->ID ) ) : ?>
		<p class="wp-team-member-posts-link">
			<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>" title="<?php echo esc_attr( $title_attr ); ?>">
				<?php
				printf(
					/* translators: %s: number of posts */
					esc_html( _n( 'View %d post', 'View %d posts', count_user_posts( $user->ID ), 'wp-team-list' ) ),
					number_format_i18n( count_user_posts( $user->ID ) )
				);
				?>
			</a>
		</p>
	<?php endif; ?>
</div>
