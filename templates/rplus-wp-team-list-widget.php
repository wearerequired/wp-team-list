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
 * @copyright 2013 required gmbh
 */
?>
<!-- This file is used to markup the public facing aspect of the plugin. -->
<!-- START: templates/rplus-wp-team-list-widget -->
<div class="<?php rplus_wp_team_list_classes( array( 'author-' . $user->ID, 'role-' . $user->roles[0] ) ); ?>">

    <figure class="author-image">
        <?php echo get_avatar( $user->ID, $size = '92', $default = '', $alt = $user->data->display_name ); ?>
    </figure>
    <h5><?php echo esc_html( $user->data->display_name ); ?></h5>
    <p><?php echo esc_html( ucfirst( $user->roles[0] ) ); ?></p>
    <p><a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>"
           title="<?php printf( esc_attr__( 'View all posts by %s', 'rplus-wp-team-list' ), $user->display_name ); ?>">
           <?php printf( _n( '%s article', '%s articles', count_user_posts( $user->ID ), 'rplus-wp-team-list' ), count_user_posts( $user->ID ) ); ?>
        </a></p>

</div>
<!-- END: templates/rplus-wp-team-list-widget -->