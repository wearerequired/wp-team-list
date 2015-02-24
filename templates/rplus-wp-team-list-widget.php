<?php
/**
 * WP Team List Widget
 *
 * This template file is used to generate the output for the WP Team List Widget
 * and can be overwritten in your theme or child theme, if you need different
 * markup.
 *
 * To change the Widget Markup rendered on the frontend, copy this file in your
 * theme folder. The final folder structure would be something like this:
 *
 * /wp-content/themes/<your theme>/rplus-wp-team-list-widget.php
 *
 * The plugin will look in your theme or child theme first and will fallback to
 * the file in the plugins folder.
 *
 * @package   WP Team List
 * @author    silvan <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2015 required gmbh
 */

/**
 * Let's begin shall we?
 *
 * You have access to the WP_User object through $user. It's for you to play
 * and render information about your team member that you want to.
 *
 * @param   int $user WP_User object for the current team member.
 *
 *          Info:   This plugin is targeted to advanced WordPress themers with a
 *                  good unterstanding of PHP and WordPress. Nevertheless here are a few
 *                  examples of what you could use in this template:
 *
 *          $user:  See what is in the user object <?php var_dump( $user ); ?>
 *                  $user->data is were most of the useful info lives.
 *                  $user->roles[0] gives back the user role on this blog.
 *                  $user->ID should be clear.
 *
 *          Meta:   get_user_meta( $user->ID, $key = '', $single = false )
 *                  All the other meta information about your user is available
 *                  through get_user_meta() like the author bio:
 *                  echo get_user_meta( $user->ID, 'description', true );
 *
 */
?>
<!-- START: templates/rplus-wp-team-list-widget -->
<div class="<?php rplus_wp_team_list_classes( array( 'author-' . $user->ID, 'role-' . $user->roles[0] ) ); ?>">

	<figure class="wp-team-member-avatar author-image">
		<?php echo get_avatar( $user->ID, $size = '92', $default = '', $alt = $user->data->display_name ); ?>
	</figure>

	<h5 class="wp-team-member-name"><?php echo esc_html( $user->data->display_name ); ?></h5>

	<p class="wp-team-member-role"><?php echo esc_html( translate_with_gettext_context( $GLOBALS['wp_roles']->roles[ $user->roles[0] ]['name'], 'User role', 'wp-team-list' ) ); ?></p>

	<?php if ( 0 < count_user_posts( $user->ID ) ) : ?>
		<p class="wp-team-member-posts-link">
			<a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>"
			   title="<?php printf( esc_attr_e( 'View all posts by %s', 'wp-team-list' ), $user->display_name ); ?>">
				<?php printf(
					esc_html( _n( 'View %d post', 'View %d posts', count_user_posts( $user->ID ), 'wp-team-list' ) ),
					count_user_posts( $user->ID )
				); ?>
			</a>
		</p>
	<?php endif; ?>
</div>
<!-- END: templates/rplus-wp-team-list-widget -->