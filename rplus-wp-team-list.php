<?php
/**
 * Plugin Name: WP Team List
 * Plugin URI:  https://github.com/wearerequired/rplus-wp-team-list
 * Description: Display your teammates anywhere on your WordPress site using this easy-to-use plugin. Provides you with a widget, a shortcode <code>[rplus_team_list]</code> and a template function <code>rplus_wp_team_list( $args, $echo = true );</code> to list the blog authors.
 * Version:     2.0.0
 * Author:      required+
 * Author URI:  http://required.ch
 * Text Domain: wp-team-list
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 * @package WP_Team_List
 */

// Load the main plugin class
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wp-team-list.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wp-team-list-widget.php' );

/**
 * Get the team list instance.
 *
 * @return \WP_Team_List Plugin instance.
 */
function wp_team_list() {
	static $wp_team_list = null;

	if ( null === $wp_team_list ) {
		$wp_team_list = new WP_Team_List();
	}

	return $wp_team_list;
}

// Initialize the plugin.
add_action( 'plugins_loaded', function () {
	wp_team_list()->add_hooks();
} );

/**
 * Render the team list.
 *
 * @param array  $args     Additional arguments for the WP_User_Query.
 * @param bool   $echo     Whether to echo the output or just return it.
 * @param string $template The template file to load for the team list.
 * @return void|string
 */
function rplus_wp_team_list( $args = array(), $echo = true, $template = 'rplus-wp-team-list.php' ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
	return wp_team_list()->render_team_list( $args, $echo, $template );
}

/**
 * Display classes for use in an item's HTML class attribute.
 *
 * @param string|array $classes List of class names.
 */
function rplus_wp_team_list_classes( $classes ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
	echo wp_team_list()->item_classes( $classes ); // WPCS: XSS ok.
}
