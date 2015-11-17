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

$role        = wp_team_list()->get_user_role( $user );
$description = get_user_meta( $user->ID, 'description', true );
?>
<div class="wp-team-member wp-team-list-item author-<?php esc_attr( $user->ID ); ?> role-<?php echo esc_attr( $role ); ?>">
	<figure class="wp-team-member-avatar author-image">
		<?php echo wp_team_list()->get_avatar( $user ); ?>
	</figure>

	<h2 class="wp-team-member-name"><?php echo esc_html( $user->data->display_name ); ?></h2>

	<?php
	if ( '' !== $role ) {
		printf( '<p class="wp-team-member-role">%s</p>', esc_html( $role ) );
	}

	if ( '' !== $description ) {
		printf( '<p class="wp-team-member-description">%s</p>', $description );
	}
	?>

	<?php if ( 0 < count_user_posts( $user->ID ) ) : ?>
		<p class="wp-team-member-posts-link">
			<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>"
			   title="<?php printf( esc_attr__( 'View all posts by %s', 'wp-team-list' ), $user->data->display_name ); ?>">
				<?php
				printf(
					esc_html( _n( 'View %d post', 'View %d posts', count_user_posts( $user->ID ), 'wp-team-list' ) ),
					count_user_posts( $user->ID )
				);
				?>
			</a>
		</p>
	<?php endif; ?>
</div>
